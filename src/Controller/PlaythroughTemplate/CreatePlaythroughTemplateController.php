<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\PlaythroughTemplate
	 * @Route(path="/templates/create", name="templates.")
	 */
	final class CreatePlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                                  $request
		 * @param PlaythroughTemplateRequestDTOTransformer $DTOtransformer
		 * @param PlaythroughTemplateEntityTransformer     $entityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request, PlaythroughTemplateRequestDTOTransformer $DTOtransformer,
			PlaythroughTemplateEntityTransformer $entityTransformer): Response {

			$playthroughTemplate = $this->createOne($request, $DTOtransformer, PlaythroughTemplateDTO::class,
				$entityTransformer);

			return $this->responseHelper->createResourceCreatedResponse('templates/read/' . $playthroughTemplate->getId());

		}
	}