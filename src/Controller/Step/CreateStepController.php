<?php
	namespace App\Controller\Step;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Step\StepDTO;
	use App\DTO\Transformer\RequestTransformer\Step\StepRequestTransformer;
	use App\Transformer\StepEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @package App\Controller
	 * @Route(path="/step/create", name="step.")
	 */
	final class CreateStepController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                $request
		 * @param StepRequestTransformer $transformer
		 * @param StepEntityTransformer  $stepEntityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request, StepRequestTransformer $transformer,
			StepEntityTransformer $stepEntityTransformer): Response {

			$step = $this->createOne($request, $transformer, StepDTO::class, $stepEntityTransformer
			);

			return $this->responseHelper->createResourceCreatedResponse('step/read/' . $step->getId());

		}

	}