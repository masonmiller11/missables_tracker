<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\Exception\ValidationException;
	use App\Repository\SectionTemplateRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\SectionTemplateEntityTransformer;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @package App\Controller
	 * @Route(path="/section/template/", name="section_template.")
	 */
	final class SectionTemplateController extends AbstractBaseApiController {

		#[Pure]
		public function __construct(
			ValidatorInterface $validator, SectionTemplateEntityTransformer $entityTransformer,
			SectionTemplateRequestTransformer $DTOTransformer,
			SectionTemplateRepository $repository
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

				$sectionTemplate = $this->createOne($request);

			} catch (ValidationException $exception) {

				return ResponseHelper::createValidationErrorResponse($exception);

			}

			return ResponseHelper::createResourceCreatedResponse('section/template/read/' . $sectionTemplate->getId());

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

			$sectionTemplate = $this->updateOne($request, $id);

			return ResponseHelper::createResourceUpdatedResponse('section/template/read/' . $sectionTemplate->getId());

		}

		protected function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}
	}