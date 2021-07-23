<?php
	namespace App\Controller\Playthrough;

	use App\Controller\AbstractBaseApiController;
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
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="list")
		 *
		 * @param string|int $page
		 *
		 * @return Response
		 */
		public function list(string|int $page): Response {

			$user = $this->getUser();
			$playthroughs = $user->getPlaythroughs();

			return $this->responseHelper->createResponse($playthroughs);

		}

	}