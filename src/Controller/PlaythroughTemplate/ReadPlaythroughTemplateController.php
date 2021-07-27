<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\PlaythroughTemplateRequestDTOTransformer;
	use App\Repository\GameRepository;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\PlaythroughTemplate
	 * @Route(path="/templates", name="templates.")
	 */
	final class ReadPlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}",methods={"read"}, name="read")
		 *
		 * @param Request                                  $request
		 * @param PlaythroughTemplateRequestDTOTransformer $transformer
		 * @param GameRepository                           $gameRepository
		 * @param PlaythroughTemplateEntityTransformer     $playthroughTemplateEntityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
							   PlaythroughTemplateRequestDTOTransformer $transformer,
							   GameRepository $gameRepository,
							   PlaythroughTemplateEntityTransformer $playthroughTemplateEntityTransformer): Response {

			$playthroughTemplate = $this->doCreate($request,
												   $transformer,
											  PlaythroughTemplateDTO::class,
												   $playthroughTemplateEntityTransformer
												   );


			return $this->responseHelper->returnResourceCreatedResponse('templates/read/' . $playthroughTemplate->getId());

		}
	}