<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\Repository\SectionRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\SectionEntityTransformer;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @package App\Controller
	 * @Route(path="/section/", name="section.")
	 */
	final class SectionController extends AbstractBaseApiController {

		#[Pure]
		public function __construct(
			ValidatorInterface $validator, SectionEntityTransformer $entityTransformer,
			SectionRequestTransformer $DTOTransformer, SectionRepository $repository
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

				$section = $this->createOne($request);

			} catch (ValidationFailedException $exception) {

				return ResponseHelper::createValidationErrorResponse($exception);

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

			$section = $this->updateOne($request, $id);

			return ResponseHelper::createResourceUpdatedResponse('section/read/' . $section->getId());

		}

		protected function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}
	}