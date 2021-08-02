<?php
	namespace App\Controller\StepTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Repository\StepTemplateRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use App\Transformer\StepEntityTransformer;
	use App\Transformer\StepTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\
	 * @Route(path="/step/template/delete", name="step_template.")
	 */
	final class DeleteStepTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int                    $id
		 * @param StepTemplateRepository        $stepTemplateRepository
		 * @param StepTemplateEntityTransformer $stepTemplateEntityTransformer
		 *
		 * @return Response
		 */
		public function delete(string|int $id, StepTemplateRepository $stepTemplateRepository,
			StepTemplateEntityTransformer $stepTemplateEntityTransformer): Response {

			$this->deleteOne($id, $stepTemplateEntityTransformer, $stepTemplateRepository);

			return $this->responseHelper->createResourceDeletedResponse();

		}
	}