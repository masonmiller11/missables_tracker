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
	 * @Route(path="/templates/create", name="templates.")
	 */
	class CreatePlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                                  $request
		 * @param PlaythroughTemplateRequestDTOTransformer $transformer
		 * @param GameRepository                           $gameRepository
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
							   PlaythroughTemplateRequestDTOTransformer $transformer,
							   GameRepository $gameRepository): Response {

			$playthroughTemplate = $this->doCreate($request,
												   $transformer,
											  PlaythroughTemplateDTO::class,
												   [new PlaythroughTemplateEntityTransformer(
												   	    $this->entityManager,
												        $this->validator,
												        $gameRepository), 'assemble'
												   ]);


			return $this->responseHelper->returnResourceCreatedResponse('templates/read/' . $playthroughTemplate->getId());

		}
	}