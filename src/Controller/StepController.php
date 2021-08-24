<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Step\StepRequestTransformer;
	use App\Repository\StepRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\StepEntityTransformer;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @package App\Controller
	 * @Route(path="/step/", name="step.")
	 */
	final class StepController extends AbstractBaseApiController {

		public function __construct(RequestStack $request, EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator, StepEntityTransformer $entityTransformer,
		                            StepRequestTransformer $DTOTransformer,
		                            StepRepository $repository) {
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
				$step = $this->createOne($request);

			} catch (ValidationFailedException $exception) {

				$errors = [];
				foreach ($exception->getViolations() as $error) {
					$errors[] = $error->getMessage();
				}

				return ResponseHelper::createValidationErrorResponse($errors);

			}

			return ResponseHelper::createResourceCreatedResponse('step/read/' . $step->getId());

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
		 * @Route(path="update/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param int $id
		 * @return Response
		 */
		public function update(Request $request, int $id): Response {

			$step = $this->updateOne($request, $id);

			return ResponseHelper::createResourceUpdatedResponse('step/read/' . $step->getId());

		}

		protected function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}
	}