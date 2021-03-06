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
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
		 *
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
		 * @Route(path="search/{searchTerm}/{page<\d+>?1}/{pageSize<\d+>?9}", methods={"GET"}, name="search_game")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param string $searchTerm
		 * @param SerializerInterface $serializer
		 * @param Request $request
		 *
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
		public function search(
			int $page, int $pageSize, string $searchTerm, SerializerInterface $serializer, Request $request
		): Response {

			try {

				if (!$this->repository instanceof GameRepository)
					throw new InvalidRepositoryException(GameRepository::class, $this->repository::class);

				try {
					//Get games that we currently have saved to the database.
					$games = $this->repository->searchByName($searchTerm, $page, $pageSize);

					if ($games['totalItems'] > 8) {

						return ResponseHelper::createReadResponse($games, $serializer);

					} else {

						//Returns an array of IGDB data transfer objects based on the search term.
						$igdbDtos = $this->IGDBHelper->searchIGDB($searchTerm, $pageSize);

						if (!$this->entityTransformer instanceof GameEntityTransformer)
							throw new InvalidArgumentException(
								'Expected GameEntityTransformer. Current transformer: ' . $this->entityTransformer::class
							);

						//Creates games found on IGDB (if they're currently not added) and then returns the Game entities.
						$igdbGames = $this->entityTransformer->assembleAndSaveMany($igdbDtos);

						//Merge games that were just created (from searching IGDB) with the games that were already in database.
						$allGames['items'] = array_merge($games['items'], $igdbGames);
						$allGames['totalItems'] = $pageSize;

						return ResponseHelper::createReadResponse($allGames, $serializer);

					}

				} catch (NotFoundHttpException) {

					//Returns an array of IGDB data transfer objects based on the search term.
					$igdbDtos = $this->IGDBHelper->searchIGDB($searchTerm, $pageSize);

					if (!$this->entityTransformer instanceof GameEntityTransformer)
						throw new InvalidArgumentException(
							'Expected GameEntityTransformer. Current transformer: ' . $this->entityTransformer::class
						);

					//Creates games found on IGDB (if they're currently not added) and then returns the Game entities.
					$igdbGames['items'] = $this->entityTransformer->assembleAndSaveMany($igdbDtos);
					$igdbGames['totalItems'] = $pageSize;

					return ResponseHelper::createReadResponse($igdbGames, $serializer);

				}

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

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
				return new JsonResponse(
					[
						'status' => 'error',
						'message' => $game . 'search returned no titles',
					], Response::HTTP_NOT_FOUND
				);
			}

			return new JsonResponse($games);

		}

	}