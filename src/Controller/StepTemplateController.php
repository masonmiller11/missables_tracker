<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Step\StepTemplateRequestTransformer;
	use App\Repository\StepTemplateRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\StepTemplateEntityTransformer;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @package App\Controller
	 * @Route(path="/step/template/", name="step_template.")
	 */
	final class StepTemplateController extends AbstractBaseApiController {

		#[Pure]
		public function __construct(
			ValidatorInterface $validator, StepTemplateEntityTransformer $entityTransformer,
			StepTemplateRequestTransformer $DTOTransformer, StepTemplateRepository $repository
		) {
			parent::__construct($validator, $entityTransformer, $DTOTransformer, $repository);
		}

		/**
		 * @Route(path="create", methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function create(Request $request): Response {

			try {

				$stepTemplate = $this->createOne($request);

			} catch (ValidationFailedException $exception) {

				return ResponseHelper::createValidationErrorResponse($exception);

			}

			return ResponseHelper::createResourceCreatedResponse('step/template/read/' . $stepTemplate->getId());

		}

		/**
		 * @Route(path="delete/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param int $id
		 *
		 * @return Response
		 */
		public function delete(int $id): Response {

			$this->deleteOne($id);

			return ResponseHelper::createResourceDeletedResponse();

		}

		/**
		 * @Route(path="update/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param int $id
		 *
		 * @return Response
		 */
		public function update(Request $request, int $id): Response {

			$stepTemplate = $this->updateOne($request, $id);

			return ResponseHelper::createResourceUpdatedResponse('step/template/read/' . $stepTemplate->getId());

		}

		protected function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}
	}