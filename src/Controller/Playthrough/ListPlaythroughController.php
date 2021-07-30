<?php
	namespace App\Controller\Playthrough;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\HttpFoundation\Response;

	/**
	 * Class ListPlaythroughController
	 *
	 * @package App\Controller
	 * @Route(path="/playthroughs", name="playthroughs.")
	 */
	final class ListPlaythroughController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param PlaythroughRepository $playthroughRepository
		 * @return Response
		 */
		public function list(int $page, int $pageSize, PlaythroughRepository $playthroughRepository): Response {

			$ownerId = $this->getUser()->getId();

			$playthroughs = $playthroughRepository->findAllByOwner($ownerId, $page, $pageSize);

			return $this->responseHelper->createReadResponse($playthroughs);

		}

	}