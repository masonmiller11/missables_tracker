<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\GameResponseDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\Entity\Game;
	use App\Repository\GameRepository;
	use App\Service\IGDBHelper;
	use App\Utility\Responder;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

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
		 * @var GameRequestDTOTransformer
		 */
		private GameRequestDTOTransformer $gameRequestDTOTransformer;

		/**
		 * @var EntityManagerInterface
		 */
		private EntityManagerInterface $entityManager;

		/**
		 * @var IGDBHelper
		 */
		private IGDBHelper $IGDBHelper;

		public function __construct (GameRepository $gameRepository,
		                             ValidatorInterface $validator,
		                             GameResponseDTOTransformer $gameResponseDTOTransformer,
									 GameRequestDTOTransformer $gameRequestDTOTransformer,
									 EntityManagerInterface $entityManager,
									 IGDBHelper $IGDBHelper	) {

			$this->gameRepository = $gameRepository;
			$this->validator = $validator;
			$this->gameResponseDTOTransformer = $gameResponseDTOTransformer;
			$this->gameRequestDTOTransformer = $gameRequestDTOTransformer;
			$this->entityManager = $entityManager;
			$this->IGDBHelper = $IGDBHelper;

		}

		/**
		 * @Route(path="/{id<\d+>}", methods={"GET"}, name="read")
		 *
		 * @param string|int $id
		 * @param SerializerInterface $serializer
		 * @return Response
		 */
		public function read(string|int $id, SerializerInterface $serializer): Response {

			$game = $this->gameRepository->find($id);

			if (!$game) {
				return new JsonResponse([
					'status' => 'error',
					'errors' => 'resource not found'
				],
					Response::HTTP_NOT_FOUND
				);
			}

			return Responder::createResponse($game, $this->gameResponseDTOTransformer);
		}

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function create(Request $request): Response {

			$dto = $this->gameRequestDTOTransformer->transformFromRequest($request);

			$errors = $this->validator->validate($dto);

			$dto->releaseDate = \DateTimeImmutable::createFromFormat('Y-m-d',$dto->releaseDate);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			}

			$game = new Game(
				$dto->genre,
				$dto->title,
				$dto->developer,
				1, //TODO this is a placeholder!
				$dto->releaseDate
			);

			$this->entityManager->persist($game);
			$this->entityManager->flush();

			return new JsonResponse(['status' => 'game created'], Response::HTTP_CREATED);
		}

		/**
		 * @Route(path="/igdf/{internetGameDatabaseID<\d+>}", methods={"GET"}, name="get_game_from_igdb")
		 *
		 * @param string|int $internetGameDatabaseID
		 * @param SerializerInterface $serializer
		 * @return Response
		 */
		public function getGameFromIGDB(string|int $internetGameDatabaseID, SerializerInterface $serializer): Response {

			$game = $this->IGDBHelper->getGame($internetGameDatabaseID);

			if (!$game) {
				return new JsonResponse([
					'status' => 'error',
					'errors' => 'resource not found'
				],
					Response::HTTP_NOT_FOUND
				);
			}

			return new JsonResponse($game, Response::HTTP_OK);
		}//$game[0]["id"]
		//id, rating, name, storyline, summary, slug, screenshots, platforms, release date, cover, artworks


	}