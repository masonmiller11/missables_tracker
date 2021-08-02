<?php
	namespace App\Controller\SectionTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Repository\SectionTemplateRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use App\Transformer\SectionTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class UpdateSectionController
	 *
	 * @package App\Controller\
	 * @Route(path="/section/template/update", name="section_template.")
	 */
	final class UpdateSectionTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request                          $request
		 * @param string|int                       $id
		 * @param SectionTemplateRepository        $sectionTemplateRepository
		 * @param SectionTemplateEntityTransformer $sectionTemplateEntityTransformer
		 *
		 * @return Response
		 */
		public function update(Request $request, string|int $id,
			SectionTemplateRepository $sectionTemplateRepository,
			SectionTemplateEntityTransformer $sectionTemplateEntityTransformer): Response {

			$sectionTemplate = $this->updateOne($request,
				$id,
				$sectionTemplateEntityTransformer,
				$sectionTemplateRepository);

			return $this->responseHelper->createResourceUpdatedResponse('section/template/read/' . $sectionTemplate->getId());

		}
	}