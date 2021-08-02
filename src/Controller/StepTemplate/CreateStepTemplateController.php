<?php
	namespace App\Controller\StepTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Step\StepDTO;
	use App\DTO\Step\StepTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Step\StepTemplateRequestTransformer;
	use App\Transformer\StepTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @package App\Controller
	 * @Route(path="/step/template/create", name="step_template.")
	 */
	final class CreateStepTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                        $request
		 * @param StepTemplateRequestTransformer $transformer
		 * @param StepTemplateEntityTransformer  $stepTemplateEntityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
			StepTemplateRequestTransformer $transformer,
			StepTemplateEntityTransformer $stepTemplateEntityTransformer): Response {

			$stepTemplate = $this->createOne($request,
				$transformer,
				StepTemplateDTO::class,
				$stepTemplateEntityTransformer
			);

			return $this->responseHelper->createResourceCreatedResponse('step/template/read/' . $stepTemplate->getId());

		}

	}