<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\PlaythroughTemplateRequestDTOTransformer;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\PlaythroughTemplate
	 * @Route(path="/templates/update", name="templates.")
	 */
	class UpdatePlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param string|int $id
		 * @param GameRepository $gameRepository
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param PlaythroughTemplateRequestDTOTransformer $playthroughTemplateRequestDTOTransformer
		 * @return Response
		 */
		public function create(Request $request, string|int $id,
							   GameRepository $gameRepository,
							   PlaythroughTemplateRepository $playthroughTemplateRepository,
							   PlaythroughTemplateRequestDTOTransformer $playthroughTemplateRequestDTOTransformer,
		                       PlaythroughTemplateEntityTransformer $playthroughTemplateEntityTransformer): Response {

			$authenticatedUser = $this->getUser();
			$template = $playthroughTemplateRepository->find($id);
			$owner =  $template->getOwner();

			if ($owner !== $authenticatedUser) {
				throw new AccessDeniedHttpException;
			}

			$playthroughTemplate = $this->doUpdate($request,
				$id,
				$playthroughTemplateEntityTransformer);

			return $this->responseHelper->returnResourceUpdatedResponse('templates/read/' . $playthroughTemplate->getId());

		}
	}