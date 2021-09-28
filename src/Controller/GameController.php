<?php
	namespace App\Controller;

	use App\Exception\InvalidRepositoryException;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\GameRepository;
	use App\Request\Payloads\GamePayload;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use App\Transformer\GameEntityTransformer;
	use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	/**
	 * Class GameController
	 *
	 * @package App\Controller
	 * @Route(path="/games/", name="games.")
	 */
	final class GameController extends AbstractBaseApiController {

		/**
		 * @var IGDBHelper
		 */
		private IGDBHelper $IGDBHelper;

		/**
		 * PayloadDecoderRegistryInterface's alias is set to PayloadDecoderRegistry in payload-decoder.yaml
		 *
		 * GameController constructor.
		 * @param IGDBHelper $IGDBHelper
		 * @param GameEntityTransformer $entityTransformer
		 * @param GameRepository $repository
		 * @param PayloadDecoderRegistryInterface $decoderRegistry
		 */
		public function __construct(
			IGDBHelper $IGDBHelper,
			GameEntityTransformer $entityTransformer,
			GameRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {

			parent::__construct(
				$entityTransformer,
				$repository,
				$decoderRegistry->getDecoder(GamePayload::class)
			);

			$this->IGDBHelper = $IGDBHelper;
		}

		/**
		 * The current flow is that we do not create games directly and instead create them when creating templates.
		 * If the template is using the IGDB id of a game that we don't have in the database, we create it at that time.
		 * You can see this in PlaythroughTemplateEntityTransformer.
		 *
		 */
//		 * @Route(path="create", methods={"POST"}, name="create")
//		 *
//		 * @param Request $request
//		 *
//		 * @return Response
//
//		public function create(Request $request): Response {
//
//			try {
//
//				$game = $this->doCreate($request);
//
//			} catch (PayloadDecoderException | ValidationException $exception) {
//
//				return $this->handleApiException($request, $exception);
//
//			}
//
//			return ResponseHelper::createResourceCreatedResponse('games/read/' . $game->getId());
//
//		}

		/**
		 * @Route(path="read/{id<\d+>}", methods={"GET"}, name="read")
		 *
		 * @param int $id
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 *
		 * Reads a single game from our database based on its id.
		 */
		public function read(int $id, SerializerInterface $serializer): Response {

			$game = $this->repository->find($id);

			return ResponseHelper::createReadResponse($game, $serializer);

		}

		/**
		 * @Route(path="popular/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="popular")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function listPopular(int $page, int $pageSize, SerializerInterface $serializer): Response {

			if (!$this->repository instanceof GameRepository)
				throw new \InvalidArgumentException(
					'GameController\'s repository not instance of type GameRepository'
				);

			$games = $this->repository->findAllOrderByTemplates($page, $pageSize);

			return ResponseHelper::createReadResponse($games, $serializer);

		}

		/**
		 * @Route(path="search/{searchTerm}", methods={"GET"}, name="search_game")
		 *
		 * @param string $searchTerm
		 * @param SerializerInterface $serializer
		 * @return Response
		 *
		 * Searches our database for title that includes the combination of letters in query.
		 * Such as ?game=halo returning Halo and Halo 4 or ?game=final returning Final Fantasy and Final Fight.
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 */
		public function search(string $searchTerm, SerializerInterface $serializer, Request $request): Response {

			try {

				if (!$this->repository instanceof GameRepository)
					throw new InvalidRepositoryException(GameRepository::class, $this->repository::class);

				//Get games that we currently have saved to the database.
				$games = $this->repository->searchByName($searchTerm);

				//Returns an array of IGDB data transfer objects.
				$igdbDtos = $this->IGDBHelper->searchIGDB($searchTerm);

				if (!$this->entityTransformer instanceof GameEntityTransformer)
					throw new InvalidArgumentException(
						'Expected GameEntityTransformer. Current transformer: ' . $this->entityTransformer::class
					);

				//Creates games found on IGDB (if they're currently not added) and then returns the Game entities.
				$igdbGames = $this->entityTransformer->createManyFromIgdbData($igdbDtos);

				$allGames = array_merge($igdbGames, $games);

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createReadResponse($allGames, $serializer);


		}

		/**
		 * @Route(path="search/igdb/{game}", methods={"GET"}, name="search_igdb")
		 *
		 * @param string $game
		 *
		 * @return Response
		 *
		 * This controller action looks at the URL query, searches IGDB and returns whatever IGDB sends us.
		 * So ?halo will return whatever IGDB finds after searching for halo. See IGDBHelper::searchIGDB()
		 *
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		public function searchIGDB(string $game): Response {

			$games = $this->IGDBHelper->searchIGDB($game);

			if (!$games || $games === []) {
				return new JsonResponse(['status' => 'error',
					'message' => $game . 'search returned no titles'
				], Response::HTTP_NOT_FOUND);
			}

			return new JsonResponse($games);

		}

	}