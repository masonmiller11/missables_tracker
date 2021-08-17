<?php
	namespace App\Controller\Like;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\LikeRepository;
	use App\Repository\StepTemplateRepository;
	use App\Transformer\LikeEntityTransformer;
	use App\Transformer\StepTemplateEntityTransformer;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\
	 * @Route(path="/like/delete", name="like.")
	 */
	final class DeleteLikeController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int            $id
		 * @param LikeRepository        $repository
		 * @param LikeEntityTransformer $entityTransformer
		 *
		 * @return Response
		 */
		public function delete(string|int $id, LikeRepository $repository,
			LikeEntityTransformer $entityTransformer): Response {

			$this->deleteOne($id, $entityTransformer, $repository);

			return $this->responseHelper->createResourceDeletedResponse();

		}

	}