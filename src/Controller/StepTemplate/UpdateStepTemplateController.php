<?php
	namespace App\Controller\StepTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
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
	 * @package App\Controller\
	 * @Route(path="/step/template/update", name="step_template.")
	 */
	final class UpdateStepTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request                       $request
		 * @param string|int                    $id
		 * @param StepTemplateRepository        $stepTemplateRepository
		 * @param StepTemplateEntityTransformer $stepTemplateEntityTransformer
		 *
		 * @return Response
		 */
		public function update(Request $request, string|int $id, StepTemplateRepository $stepTemplateRepository,
			StepTemplateEntityTransformer $stepTemplateEntityTransformer): Response {

			$stepTemplate = $this->updateOne($request, $id, $stepTemplateEntityTransformer, $stepTemplateRepository);

			return $this->responseHelper->createResourceUpdatedResponse('step/template/read/' . $stepTemplate->getId());

		}
	}