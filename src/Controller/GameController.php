<?php

	namespace App\Controller;

	use App\DTO\Transformer\GameResponseDTOTransformer;
	use App\Entity\Game;
	use App\Entity\PlaythroughTemplate;
	use App\Repository\GameRepository;
	use JetBrains\PhpStorm\ArrayShape;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Constraints\Json;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class GameController extends AbstractController {

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var ValidatorInterface
		 */
		private ValidatorInterface $validator;
		private GameResponseDTOTransformer $gameResponseDTOTransformer;

		public function __construct (GameRepository $gameRepository,
		                             ValidatorInterface $validator,
		                             GameResponseDTOTransformer $gameResponseDTOTransformer) {
			$this->gameRepository = $gameRepository;
			$this->validator = $validator;
			$this->gameResponseDTOTransformer = $gameResponseDTOTransformer;
		}

		/**
		 * @Route(path="/games/{id<\d+>}", methods={"GET"}, name="games.read")
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

			$errors = $this->validator->validate($dto);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			}

			return new Response($serializer->serialize($dto, 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;
		}

		/**
		 * @Route(path="/games/{id<\d+>}", methods={"POST"}, name="games.create")
		 *
		 * @param Request $request
		 * @return Response
		 */
		public function create(Request $request): Response {
			$data = json_decode($request->getContent(),true);

			return new JsonResponse([
				'status' => 'ok'
			],
			Response::HTTP_CREATED);
		}

	}