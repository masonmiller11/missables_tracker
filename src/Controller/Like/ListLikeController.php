<?php
	namespace App\Controller\Like;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\LikeRepository;
	use App\Repository\PlaythroughRepository;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class ListLikeController
	 * @Route(path="/likes", name="like.")
	 */
	final class ListLikeController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param LikeRepository $likeRepository
		 * @return Response
		 */
		public function list(int $page, int $pageSize, LikeRepository $likeRepository): Response {

			$ownerId = $this->getUser()->getId();

			$playthroughs = $likeRepository->findAllByOwner($ownerId, $page, $pageSize);

			return $this->responseHelper->createReadResponse($playthroughs);

		}

	}