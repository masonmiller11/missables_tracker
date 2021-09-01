<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use App\Transformer\GameEntityTransformer;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;
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
		 * GameController constructor.
		 * @param IGDBHelper $IGDBHelper
		 * @param ValidatorInterface $validator
		 * @param GameEntityTransformer $entityTransformer
		 * @param GameRequestDTOTransformer $DTOTransformer
		 * @param GameRepository $repository
		 */
		#[Pure]
		public function __construct(
			IGDBHelper $IGDBHelper, ValidatorInterface $validator,
			GameEntityTransformer $entityTransformer, GameRequestDTOTransformer $DTOTransformer,
			GameRepository $repository
		) {

			parent::__construct($validator, $entityTransformer, $DTOTransformer, $repository);

			$this->IGDBHelper = $IGDBHelper;

		}

		/**
		 * @Route(path="create", methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function create(Request $request): Response {

			try {

				$game = $this->createOne($request);

			} catch (ValidationException $exception) {


				return ResponseHelper::createValidationErrorResponse($exception);

			}

			return ResponseHelper::createResourceCreatedResponse('games/read/' . $game->getId());

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
		 * @Route(path="search/{game}", methods={"GET"}, name="search_game")
		 *
		 * @return Response
		 *
		 * Searches our database for title that includes the combination of letters in query.
		 * Such as ?game=halo returning Halo and Halo 4 or ?game=final returning Final Fantasy and Final Fight.
		 */
		public function search(string $game, SerializerInterface $serializer): Response {

			if (!$this->repository instanceof GameRepository)
				throw new \InvalidArgumentException(
					'repository is not of type GameRespository'
				);

			$games = $this->repository->searchByName($game);

			return ResponseHelper::createReadResponse($games, $serializer);

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
					'message' => $game . 'returned no titles'
				], Response::HTTP_NOT_FOUND);
			}

			return new JsonResponse($games);

		}

		protected function update(Request $request, int $id): Response {
			// TODO: Implement update() method.
		}

		protected function delete(int $id): Response {
			// TODO: Implement delete() method.
		}

	}