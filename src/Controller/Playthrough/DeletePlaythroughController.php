<?php
	namespace App\Controller\Playthrough;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class ListPlaythroughController
	 *
	 * @package App\Controller
	 * @Route(path="/playthroughs/delete", name="playthroughs.")
	 */
	class DeletePlaythroughController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
		 * @param PlaythroughRepository $playthroughRepository
		 * @param PlaythroughEntityTransformer $playthroughEntityTransformer
		 *
		 * @return Response
		 */
		public function delete(string|int $id,
			PlaythroughRepository $playthroughRepository,
			PlaythroughEntityTransformer $playthroughEntityTransformer): Response {

			$this->deleteOne($id, $playthroughEntityTransformer, $playthroughRepository);

			return $this->responseHelper->createResourceDeletedResponse();

		}

	}