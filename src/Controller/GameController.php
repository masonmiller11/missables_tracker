<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\GameResponseDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\Entity\Game;
	use App\Repository\GameRepository;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use App\Utility\Responder;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

		public function __construct (GameRepository $gameRepository,
		                             ValidatorInterface $validator,
		                             GameResponseDTOTransformer $gameResponseDTOTransformer,
									 EntityManagerInterface $entityManager,
									 IGDBHelper $IGDBHelper,
									 ResponseHelper $responseHelper) {

			$this->gameRepository = $gameRepository;
			$this->validator = $validator;
			$this->gameResponseDTOTransformer = $gameResponseDTOTransformer;
			$this->entityManager = $entityManager;
			$this->IGDBHelper = $IGDBHelper;
			$this->responseHelper = $responseHelper;

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

			return $this->responseHelper->createResponseForOne($game, $this->gameResponseDTOTransformer);

		}

		//TODO read from IGDF ID (if game goes back, yay, if not get from IGDF and create. Then read from IGDF again.

		/**
		 * @Route(path="/read/igdf/{internetGameDatabaseID<\d+>}", methods={"GET"}, name="get_game_from_igdb")
		 *
		 * @param string|int          $internetGameDatabaseID
		 * @param SerializerInterface $serializer
		 * @return Response
		 * @throws TransportExceptionInterface
		 */
		public function getGameFromIGDB(string|int $internetGameDatabaseID, SerializerInterface $serializer): Response {

			$dto = $this->IGDBHelper->getGame($internetGameDatabaseID);

			$errors = $this->validator->validate($dto);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			}

			$gameIfInDatabase = $this->IGDBHelper->isIGDBGameInDatabase($dto);

			if ($gameIfInDatabase) {

				return $this->responseHelper->createResponseForOne($gameIfInDatabase, $this->gameResponseDTOTransformer);

			} else {

				$game = new Game($dto->genre, $dto->title, $dto->id, $dto->screenshots, $dto->artworks, $dto->cover,
								$dto->platforms,$dto->slug, $dto->rating, $dto->summary, $dto->storyline,
								$dto->releaseDate
				);

				$this->entityManager->persist($game);
				$this->entityManager->flush();

				return $this->responseHelper->createResponseForOne($game, $this->gameResponseDTOTransformer);

			}

		}


	}