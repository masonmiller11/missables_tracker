<?php
	namespace App\Controller\Section;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\
	 * @Route(path="/section/delete", name="section.")
	 */
	final class DeleteSectionController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int               $id
		 * @param SectionRepository        $sectionRepository
		 * @param SectionEntityTransformer $sectionEntityTransformer
		 *
		 * @return Response
		 */
		public function delete(string|int $id, SectionRepository $sectionRepository,
			SectionEntityTransformer $sectionEntityTransformer): Response {

			$this->deleteOne($id, $sectionEntityTransformer, $sectionRepository);

			return $this->responseHelper->createResourceDeletedResponse();

		}
	}