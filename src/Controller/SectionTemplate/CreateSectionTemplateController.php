<?php
	namespace App\Controller\SectionTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\Transformer\SectionTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @package App\Controller
	 * @Route(path="/section/template/create", name="section_template.")
	 */
	final class CreateSectionTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                           $request
		 * @param SectionTemplateRequestTransformer $transformer
		 * @param SectionTemplateEntityTransformer  $sectionTemplateEntityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
			SectionTemplateRequestTransformer $transformer,
			SectionTemplateEntityTransformer $sectionTemplateEntityTransformer): Response {

			$sectionTemplate = $this->doCreate($request,
				$transformer,
				SectionTemplateDTO::class,
				$sectionTemplateEntityTransformer
			);

			return $this->responseHelper->createResourceCreatedResponse('section/template/read/' . $sectionTemplate->getId());

		}

	}