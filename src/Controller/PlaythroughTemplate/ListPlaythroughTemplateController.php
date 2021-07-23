<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughTemplateRepository;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\HttpFoundation\Response;

	/**
	 * Class ListPlaythroughTemplateController
	 *
	 * @package App\Controller
	 * @Route(path="/templates", name="templates.")
	 */
	final class ListPlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="list_this_users")
		 *
		 * @param string|int $page
		 *
		 * @return Response
		 */
		public function listThisUsers(string|int $page): Response {

			$user = $this->getUser();
			$templates = $user->getPlaythroughTemplates();

			return $this->responseHelper->createResponse($templates);

		}

		/**
		 * @Route(path="/bygame/{gameID<\d+>}", methods={"GET"}, name="list_by_game")
		 *
		 * @param int                           $gameID
		 * @param PlaythroughTemplateRepository $repository
		 *
		 * @return Response
		 */
		public function listByGame (int $gameID, PlaythroughTemplateRepository $repository ): Response {

				$templates = $repository->findByGame($gameID);
				return $this->responseHelper->createResponse($templates);

		}

		/**
		 * @Route(path="/byauthor/{authorID<\d+>}", methods={"GET"}, name="list_by_author")
		 *
		 * @param int                           $authorID
		 * @param PlaythroughTemplateRepository $repository
		 *
		 * @return Response
		 */
		public function listByAuthor (int $authorID, PlaythroughTemplateRepository $repository): Response {

			$templates = $repository->findByAuthor($authorID);
			return $this->responseHelper->createResponse($templates);


		}

	}