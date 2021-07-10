<?php
	namespace App\Controller;

	use App\DTO\Response\IGDBResponseDTO;
	use App\DTO\Transformer\ResponseTransformer\GameResponseDTOTransformer;
	use App\Entity\Game;
	use App\Repository\GameRepository;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;
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

			/**
			 * returns an IGDBResponseDTO with data from IGDB
			 * @see IGDBResponseDTO
			 */
			$dto = $this->IGDBHelper->getGame($internetGameDatabaseID);

			/**
			 * Validate DTO
			 */
			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			}

			/**
			 * Returns a Game entity if it's in database.
			 * @see Game
			 */
			$gameIfInDatabase = $this->IGDBHelper->isIGDBGameInDatabase($dto);

			/**
			 * If $gameIfInDatabase is not present, then create a new Game entity and return it in response.
			 */
			if (!$gameIfInDatabase) {

				//TODO update gameRequestDTO, create EntityHelper with CreateEntity method.

				$game = new Game($dto->genre, $dto->title, $dto->id, $dto->screenshots, $dto->artworks, $dto->cover,
					$dto->platforms,$dto->slug, $dto->rating, $dto->summary, $dto->storyline,
					$dto->releaseDate
				);

				$this->entityManager->persist($game);
				$this->entityManager->flush();

				return $this->responseHelper->createResponseForOne($game, $this->gameResponseDTOTransformer);
			}

			/**
			 * Otherwise, return the Game entity in response.
			 */
			return $this->responseHelper->createResponseForOne($gameIfInDatabase, $this->gameResponseDTOTransformer);


		}

	}