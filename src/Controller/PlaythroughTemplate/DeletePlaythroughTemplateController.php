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
	 * @Route(path="/templates/delete", name="templates.")
	 */
	final class DeletePlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param PlaythroughTemplateEntityTransformer $playthroughTemplateEntityTransformer
		 * @return Response
		 */
		public function delete(string|int $id,
							   PlaythroughTemplateRepository $playthroughTemplateRepository,
		                       PlaythroughTemplateEntityTransformer $playthroughTemplateEntityTransformer): Response {

			$this->confirmResourceOwner($playthroughTemplateRepository->find($id));

			$this->doDelete($id, $playthroughTemplateEntityTransformer);

			return $this->responseHelper->returnResourceDeletedResponse();

		}
	}