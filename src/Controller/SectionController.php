<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\SectionRepository;
	use App\Request\Payloads\SectionPayload;
	use App\Service\ResponseHelper;
	use App\Transformer\SectionEntityTransformer;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @package App\Controller
	 * @Route(path="/section/", name="section.")
	 */
	final class SectionController extends AbstractBaseApiController implements BaseApiControllerInterface {

		public function __construct(
			ValidatorInterface $validator,
			SectionEntityTransformer $entityTransformer,
			SectionRequestTransformer $DTOTransformer,
			SectionRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {

			parent::__construct(
				$validator,
				$entityTransformer,
				$DTOTransformer,
				$repository,
				$decoderRegistry->getDecoder(SectionPayload::class)
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

				$section = $this->doCreate($request, $this->getUser());

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceCreatedResponse('section/read/' . $section->getId());

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

			try {

				$section = $this->doUpdate($request, $id);

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceUpdatedResponse('section/read/' . $section->getId());

		}

		public function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}
	}