<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\GameResponseDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\Entity\Game;
	use App\Repository\GameRepository;
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

		public function __construct (GameRepository $gameRepository,
		                             ValidatorInterface $validator,
		                             GameResponseDTOTransformer $gameResponseDTOTransformer,
									 GameRequestDTOTransformer $gameRequestDTOTransformer,
									 EntityManagerInterface $entityManager) {

			$this->gameRepository = $gameRepository;
			$this->validator = $validator;
			$this->gameResponseDTOTransformer = $gameResponseDTOTransformer;
			$this->gameRequestDTOTransformer = $gameRequestDTOTransformer;
			$this->entityManager = $entityManager;

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

			$dto = $this->gameResponseDTOTransformer->transformFromObject($game);

			return Responder::createResponse($dto);
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
				$dto->releaseDate,
			);

			$this->entityManager->persist($game);
			$this->entityManager->flush();

			return new JsonResponse([
				'status' => 'game created'
			],
			Response::HTTP_CREATED);
		}

	}