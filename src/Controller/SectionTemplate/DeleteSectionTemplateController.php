<?php
	namespace App\Controller\SectionTemplate;

	use App\Controller\AbstractBaseApiController;
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
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\
	 * @Route(path="/section/template/delete", name="section_template.")
	 */
	final class DeleteSectionTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int                       $id
		 * @param SectionTemplateRepository        $sectionTemplateRepository
		 * @param SectionTemplateEntityTransformer $sectionTemplateEntityTransformer
		 *
		 * @return Response
		 */
		public function delete(string|int $id,
			SectionTemplateRepository $sectionTemplateRepository,
			SectionTemplateEntityTransformer $sectionTemplateEntityTransformer): Response {

			$this->deleteOne($id, $sectionTemplateEntityTransformer, $sectionTemplateRepository);

			return $this->responseHelper->createResourceDeletedResponse();

		}
	}