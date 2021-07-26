<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
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
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param PlaythroughTemplateEntityTransformer $playthroughTemplateEntityTransformer
		 * @return Response
		 */
		public function update(Request $request, string|int $id,
							   PlaythroughTemplateRepository $playthroughTemplateRepository,
		                       PlaythroughTemplateEntityTransformer $playthroughTemplateEntityTransformer): Response {

			$this->confirmResourceOwner($playthroughTemplateRepository->find($id));

			$playthroughTemplate = $this->doUpdate($request,
				$id,
				$playthroughTemplateEntityTransformer);

			return $this->responseHelper->returnResourceUpdatedResponse('templates/read/' . $playthroughTemplate->getId());

		}
	}