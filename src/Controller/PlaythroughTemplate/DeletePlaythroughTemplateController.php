<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Transformer\PlaythroughEntityTransformer;
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
		 * @param PlaythroughEntityTransformer $playthroughTemplateEntityTransformer
		 * @return Response
		 */
		public function delete(string|int $id, PlaythroughTemplateRepository $playthroughTemplateRepository,
			PlaythroughEntityTransformer $playthroughTemplateEntityTransformer): Response {

			$this->deleteOne($id, $playthroughTemplateEntityTransformer, $playthroughTemplateRepository);

			return $this->responseHelper->createResourceDeletedResponse();

		}
	}