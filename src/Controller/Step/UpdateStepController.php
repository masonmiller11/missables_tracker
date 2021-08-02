<?php
	namespace App\Controller\Step;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use App\Transformer\StepEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @package App\Controller\
	 * @Route(path="/step/update", name="step.")
	 */
	final class UpdateStepController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request               $request
		 * @param string|int            $id
		 * @param StepRepository        $stepRepository
		 * @param StepEntityTransformer $stepEntityTransformer
		 *
		 * @return Response
		 */
		public function update(Request $request, string|int $id,
			StepRepository $stepRepository,
			StepEntityTransformer $stepEntityTransformer): Response {

			$step = $this->updateOne($request,
				$id,
				$stepEntityTransformer,
				$stepRepository);

			return $this->responseHelper->createResourceUpdatedResponse('step/read/' . $step->getId());

		}
	}