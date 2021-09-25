<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Step\StepRequestTransformer;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\StepRepository;
	use App\Request\Payloads\StepPayload;
	use App\Service\ResponseHelper;
	use App\Transformer\StepEntityTransformer;
	use Doctrine\ORM\Query\Expr\Base;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @package App\Controller
	 * @Route(path="/step/", name="step.")
	 */
	final class StepController extends AbstractBaseApiController implements BaseApiControllerInterface {

		#[Pure]
		public function __construct(
			ValidatorInterface $validator,
			StepEntityTransformer $entityTransformer,
			StepRequestTransformer $DTOTransformer,
			StepRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {
			parent::__construct(
				$validator,
				$entityTransformer,
				$DTOTransformer,
				$repository,
				$decoderRegistry->getDecoder(StepPayload::class)
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

				$step = $this->doCreate($request, $this->getUser());

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceCreatedResponse('step/read/' . $step->getId());

		}

		/**
		 * @Route(path="delete/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
		 *
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
		 *
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