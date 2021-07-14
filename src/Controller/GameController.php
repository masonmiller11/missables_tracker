<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\DTO\Transformer\ResponseTransformer\GameResponseDTOTransformer;
	use App\Repository\GameRepository;
	use App\Service\EntityHelper;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;
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
	 * @Route(path="/games", name="games.")
	 */
	class GameController extends AbstractController {

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var GameResponseDTOTransformer
		 */
		private GameResponseDTOTransformer $gameResponseDTOTransformer;

		/**
		 * @var IGDBHelper
		 */
		private IGDBHelper $IGDBHelper;

		/**
		 * @var ResponseHelper
		 */
		private ResponseHelper $responseHelper;

		/**
		 * @var GameRequestDTOTransformer
		 */
		private GameRequestDTOTransformer $gameRequestDTOTransformer;

		/**
		 * @var EntityHelper
		 */
		private EntityHelper $entityHelper;

		/**
		 * @var RequestStack
		 */
		private RequestStack $request;

		public function __construct (GameRepository $gameRepository,
		                             GameResponseDTOTransformer $gameResponseDTOTransformer,
									 IGDBHelper $IGDBHelper,
									 ResponseHelper $responseHelper,
									 GameRequestDTOTransformer $gameRequestDTOTransformer,
									 EntityHelper $entityHelper,
									 RequestStack $request) {

			$this->gameRepository = $gameRepository;
			$this->gameResponseDTOTransformer = $gameResponseDTOTransformer;
			$this->IGDBHelper = $IGDBHelper;
			$this->responseHelper = $responseHelper;
			$this->gameRequestDTOTransformer = $gameRequestDTOTransformer;
			$this->entityHelper = $entityHelper;
			$this->request = $request;

		}

		/**
		 * @Route(path="/read/{id<\d+>}", methods={"GET"}, name="read")
		 *
		 * @param string|int $id
		 * @param SerializerInterface $serializer
		 * @return Response
		 *
		 * Reads a single game from our database based on its id.
		 */
		public function read(string|int $id, SerializerInterface $serializer): Response {

			try {

				/**
				 * Gets a a game and runs it through ResponseHelper. Inside ResponseHelper, we take the game object,
				 * turn it into a DTO and then validate it before returning.
				 * @See GameResponseDTOTransformer
				 * @See ResponseHelper
				 */
				$game = $this->gameRepository->find($id);
				return $this->responseHelper->createResponseForOne($game, $this->gameResponseDTOTransformer);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

		/**
		 * @Route(path="/create", methods={"POST"}, name="games.create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 * @throws \Exception
		 *
		 * Creates a game based off of request body.
		 */
		public function create(Request $request): Response {

			$dto = $this->gameRequestDTOTransformer->transformFromRequest($request);

			try {

				/**
				 * entityHelper->createGame validates gameRequestDTO before submitting to database. Returns Game entity
				 */
				$this->entityHelper->createGame($dto);
				return new JsonResponse([
					'status' => 'game created'
				],
					Response::HTTP_CREATED);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

		/**
		 * @Route(path="/search", methods={"GET"}, name="search_game")
		 *
		 * @param SerializerInterface $serializer
		 * @return Response
		 *
		 * Searches our database for title that includes the combination of letters in query.
		 * Such as ?game=halo returning Halo and Halo 4 or ?game=final returning Final Fantasy and Final Fight.
		 */
		public function search(SerializerInterface $serializer): Response {

			try {

				$searchTerm = $this->request->getCurrentRequest()->query->get('game');
				$games = $this->gameRepository->searchByName($searchTerm);

				/**
				 * Inside ResponseHelper, we take the game object, turn it into a DTO and then validate it before returning.
				 * @See GameResponseDTOTransformer
				 * @See ResponseHelper
				 */
				return $this->responseHelper->createResponseForMany($games, $this->gameResponseDTOTransformer);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

		/**
		 * @Route(path="/popular", methods={"GET"}, name="list_popular")
		 *
		 * @param SerializerInterface $serializer
		 * @return Response
		 *
		 * Queries the database for games, orders them by number of playthroughTemplates belonging and returns 10.
		 */
		public function listPopular(SerializerInterface $serializer): Response {

			try {

				$games = $this->gameRepository->topTenByNumberOfTemplates();

				/**
				 * Inside ResponseHelper, we take the game objects, turn them into an array of DTOs and then validate it before returning.
				 * @See GameResponseDTOTransformer
				 * @See ResponseHelper
				 */
				return $this->responseHelper->createResponseForMany($games, $this->gameResponseDTOTransformer);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

		/**
		 * @Route(path="/search/igdb", methods={"GET"}, name="search_igdb")
		 *
		 * @param SerializerInterface $serializer
		 * @return Response
		 * @throws TransportExceptionInterface
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 *
		 * This controller action looks at the URL query, searches IGDB and returns whatever IGDB sends us.
		 * So ?halo will return whatever IGDB poops up if we searched for Halo.
		 */
		public function searchIGDB(SerializerInterface $serializer): Response {

			try {

				$searchTerm = $this->request->getCurrentRequest()->query->get('game');

				/**
				 * This converts the response from IGDB to an array and returns it.
				 */
				$games = $this->IGDBHelper->searchIGDB($searchTerm);
				return new JsonResponse($games); //TODO what happens if we search for gibberish?

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

		/**
		 * @Route(path="/read/igdf/{internetGameDatabaseID<\d+>}", methods={"GET"}, name="get_game_from_igdb")
		 *
		 * @param string|int          $internetGameDatabaseID
		 * @param SerializerInterface $serializer
		 * @return Response
		 * @throws TransportExceptionInterface
		 *
		 * Gets a game from IGDB. If it's already in our database, return Game entity from, if it's not in our database,
		 * create it with the data from IGDB and then return that new Game entity.
		 */
		public function getGameFromIGDB(string|int $internetGameDatabaseID, SerializerInterface $serializer): Response {

			try{
				/**
				 * getGameAndSave uses the ID to retrieve data from IGDB, transforms it into a
				 * IGDBResponseDTO, then validates it before using that DTO to create a Game entity or to retrieve a
				 * Game entity from our database.
				 *
				 * createResponseForOne takes the Game, transforms it into a DTO, validates it, and then returns
				 * the gameResponseDTO.
				 */
				$game = $this->IGDBHelper->getGameAndSave($internetGameDatabaseID);
				return $this->responseHelper->createResponseForOne($game, $this->gameResponseDTOTransformer);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

	}