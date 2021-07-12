<?php
	namespace App\Controller;

	use App\DTO\IGDBGameResponseDTO;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\DTO\Transformer\ResponseTransformer\GameResponseDTOTransformer;
	use App\Entity\Game;
	use App\Repository\GameRepository;
	use App\Service\EntityHelper;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use Doctrine\DBAL\Exception;
	use Doctrine\ORM\EntityManagerInterface;
	use Lcobucci\JWT\Signer\Ecdsa\ConversionFailed;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;
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
	 * @Route(path="/games", name="games.")
	 */
	class GameController extends AbstractController {

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var ValidatorInterface
		 */
		private ValidatorInterface $validator;

		/**
		 * @var GameResponseDTOTransformer
		 */
		private GameResponseDTOTransformer $gameResponseDTOTransformer;

		/**
		 * @var EntityManagerInterface
		 */
		private EntityManagerInterface $entityManager;

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
		                             ValidatorInterface $validator,
		                             GameResponseDTOTransformer $gameResponseDTOTransformer,
									 EntityManagerInterface $entityManager,
									 IGDBHelper $IGDBHelper,
									 ResponseHelper $responseHelper,
									 GameRequestDTOTransformer $gameRequestDTOTransformer,
									 EntityHelper $entityHelper,
									 RequestStack $request) {

			$this->gameRepository = $gameRepository;
			$this->validator = $validator;
			$this->gameResponseDTOTransformer = $gameResponseDTOTransformer;
			$this->entityManager = $entityManager;
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
		 */
		public function read(string|int $id, SerializerInterface $serializer): Response {

			$game = $this->gameRepository->find($id);

			return $this->responseHelper->createResponseForOne($game, $this->gameResponseDTOTransformer);

		}

		/**
		 * @Route(path="/search/igdb", methods={"GET"}, name="search")
		 *
		 * @param SerializerInterface $serializer
		 * @return Response
		 * @throws TransportExceptionInterface
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 */
		public function searchIGDB(SerializerInterface $serializer): Response {

			try {

				$searchTerm = $this->request->getCurrentRequest()->query->get('game');

				$games = $this->IGDBHelper->searchIGDB($searchTerm);

				return new JsonResponse($games);

			} catch (\Exception $e) {
				return new JsonResponse(['status' => 'error', 'errors' => strval($e)], Response::HTTP_INTERNAL_SERVER_ERROR);

			}
		}

		/**
		 * @Route(path="/create", methods={"POST"}, name="games.create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request): Response {

			$dto = $this->gameRequestDTOTransformer->transformFromRequest($request);

			try {
				$this->entityHelper->createGame($dto);
				return new JsonResponse([
					'status' => 'game created'
				],
					Response::HTTP_CREATED);
			} catch (\Exception $e) {
				return new JsonResponse(['status' => 'error', 'errors' => strval($e)], Response::HTTP_INTERNAL_SERVER_ERROR);
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
		 * Gets a game from IGDB. If it's already in our database, return Game entity, if it's not in our database,
		 * create it with the data from IGDB and then return that new Game entity.
		 */
		public function getGameFromIGDB(string|int $internetGameDatabaseID, SerializerInterface $serializer): Response {

			try{

				$game = $this->IGDBHelper->getGameAndSave($internetGameDatabaseID);

			} catch (\Exception $e) {

				return new JsonResponse(['status' => 'error', 'errors' => strval($e)], Response::HTTP_INTERNAL_SERVER_ERROR);

//				return new Response('There was an issue with this request. ' . $e);

			}

			return $this->responseHelper->createResponseForOne($game, $this->gameResponseDTOTransformer);

		}

	}