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

		public function __construct (GameRepository $gameRepository,
		                             ValidatorInterface $validator,
		                             GameResponseDTOTransformer $gameResponseDTOTransformer,
									 EntityManagerInterface $entityManager,
									 IGDBHelper $IGDBHelper	) {

			$this->gameRepository = $gameRepository;
			$this->validator = $validator;
			$this->gameResponseDTOTransformer = $gameResponseDTOTransformer;
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

			//TODO redo this DTO and transformer
			return Responder::createResponseFromObject($game, $this->gameResponseDTOTransformer);
		}

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

			if ($this->IGDBHelper->isIGDBGameInDatabase($dto)) {

				return new JsonResponse([$dto, 'status' => 'game already in database'], Response::HTTP_OK);

			} else {

				$game = new Game('placeholder', $dto->title, $dto->id, $dto->screenshots, $dto->artworks, $dto->cover,
								$dto->platforms,$dto->slug, $dto->rating, $dto->summary, $dto->storyline,
								$dto->releaseDate
				);

				$this->entityManager->persist($game);
				$this->entityManager->flush();

				return new JsonResponse([$dto, 'status' => 'game added to database'], Response::HTTP_CREATED);
			}

		}


	}