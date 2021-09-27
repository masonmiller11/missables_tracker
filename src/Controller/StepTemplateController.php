<?php
	namespace App\Controller;

	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\StepTemplateRepository;
	use App\Request\Payloads\StepTemplatePayload;
	use App\Service\ResponseHelper;
	use App\Transformer\StepTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;

	/**
	 * @package App\Controller
	 * @Route(path="/step/template/", name="step_template.")
	 */
	final class StepTemplateController extends AbstractBaseApiController implements BaseApiControllerInterface {

		/**
		 * StepTemplateController constructor.
		 * @param StepTemplateEntityTransformer $entityTransformer
		 * @param StepTemplateRepository $repository
		 * @param PayloadDecoderRegistryInterface $decoderRegistry
		 */
		public function __construct(
			StepTemplateEntityTransformer $entityTransformer,
			StepTemplateRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {
			parent::__construct(
				$entityTransformer,
				$repository,
				$decoderRegistry->getDecoder(StepTemplatePayload::class)
			);
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

				$stepTemplate = $this->doCreate($request, $this->getUser());

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

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

			try {

				$stepTemplate = $this->doUpdate($request, $id);

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceUpdatedResponse('step/template/read/' . $stepTemplate->getId());

		}

		public function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}
	}