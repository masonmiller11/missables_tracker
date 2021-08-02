<?php
	namespace App\Controller\Section;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class UpdateSectionController
	 *
	 * @package App\Controller\
	 * @Route(path="/section/update", name="section.")
	 */
	final class UpdateSectionController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request                  $request
		 * @param string|int               $id
		 * @param SectionRepository        $sectionRepository
		 * @param SectionEntityTransformer $sectionEntityTransformer
		 *
		 * @return Response
		 */
		public function update(Request $request, string|int $id,
			SectionRepository $sectionRepository,
			SectionEntityTransformer $sectionEntityTransformer): Response {

			$section = $this->updateOne($request,
				$id,
				$sectionEntityTransformer,
				$sectionRepository);

			return $this->responseHelper->createResourceUpdatedResponse('section/read/' . $section->getId());

		}
	}