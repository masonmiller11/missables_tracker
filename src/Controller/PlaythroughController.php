<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughRequestDTOTransformer;
	use App\Repository\PlaythroughRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\PlaythroughEntityTransformer;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @Route(path="/playthroughs/", name="playthroughs.")
	 */
	final class PlaythroughController extends AbstractBaseApiController {

		public function __construct(RequestStack $request, EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator, PlaythroughEntityTransformer $entityTransformer,
		                            PlaythroughRequestDTOTransformer $DTOTransformer, PlaythroughRepository $repository) {

			parent::__construct($request, $entityManager, $validator, $entityTransformer, $DTOTransformer, $repository);

		}

		/**
		 * @Route(path="create", methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 * @return Response
		 */
		public function create(Request $request): Response {

			try {

				$playthrough = $this->createOne($request);

			} catch (ValidationFailedException $exception) {

				$errors = [];

				foreach ($exception->getViolations() as $error) {
					$errors[] = $error->getMessage();
				}

				return ResponseHelper::createValidationErrorResponse($errors);

			}

			return ResponseHelper::createResourceCreatedResponse('playthroughs/read/' . $playthrough->getId());

		}

		/**
		 * @Route(path="delete/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
		 * @return Response
		 */
		public function delete(string|int $id): Response {

			$this->deleteOne($id);

			return ResponseHelper::createResourceDeletedResponse();

		}

		/**
		 * @Route(path="/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @return Response
		 */
		public function list(int $page, int $pageSize, SerializerInterface $serializer): Response {

			$ownerId = $this->getUser()->getId();

			if (!$this->repository instanceof PlaythroughRepository) throw new \InvalidArgumentException(
				'repository not instance of type PlaythroughRepository'
			);

			$playthroughs = $this->repository->findAllByOwner($ownerId, $page, $pageSize);

			return ResponseHelper::createReadResponse($playthroughs, $serializer);

		}

		/**
		 * @Route(path="read/{id<\d+>}",methods={"GET"}, name="read")
		 *
		 * @param int $id
		 * @return Response
		 */
		public function read(int $id, SerializerInterface $serializer): Response {

			$playthrough = $this->repository->find($id);

			return ResponseHelper::createReadResponse($playthrough, $serializer);

		}

		/**
		 * @Route(path="update/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param string|int $id
		 * @return Response
		 */
		public function update(Request $request, string|int $id): Response {

			$playthroughTemplate = $this->updateOne($request, $id);

			return ResponseHelper::createResourceUpdatedResponse('playthroughs/read/' . $playthroughTemplate->getId());

		}

	}