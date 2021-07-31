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
	 * @Route(path="/templates", name="templates.list_")
	 */
	final class ListPlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="this_users")
		 *
		 * @param string|int $page
		 *
		 * @return Response
		 */
		public function listThisUsers(string|int $page): Response {

			//TODO use paginator for this in future.

			$user = $this->getUser();
			$templates = $user->getPlaythroughTemplates();

			return $this->responseHelper->createReadResponse($templates);

		}

		/**
		 * @Route(path="/bygame/{gameID<\d+>}/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="by_game")
		 *
		 * @param int $gameID
		 * @param int $page
		 * @param int $pageSize
		 * @param PlaythroughTemplateRepository $repository
		 *
		 * @return Response
		 */
		public function listByGame (int $gameID,
		                            int $page,
		                            int $pageSize,
		                            PlaythroughTemplateRepository $repository ): Response {

				$templates = $repository->findAllByGame($gameID, $page, $pageSize);
				return $this->responseHelper->createReadResponse($templates);

		}

		/**
		 * @Route(path="/byauthor/{authorID<\d+>}/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="by_author")
		 *
		 * @param int $authorID
		 * @param int $page
		 * @param int $pageSize
		 * @param PlaythroughTemplateRepository $repository
		 *
		 * @return Response
		 */
		public function listByAuthor (int $authorID,
		                              int $page,
		                              int $pageSize,
		                              PlaythroughTemplateRepository $repository): Response {

			$templates = $repository->findAllByAuthor($authorID, $page, $pageSize);
			return $this->responseHelper->createReadResponse($templates);


		}

		/**
		 * @Route(path="/all/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="all")
		 *
		 * @param PlaythroughTemplateRepository $repository
		 * @param int $page
		 * @param int $pageSize
		 * @return Response
		 */
		public function list (PlaythroughTemplateRepository $repository, int $page, int $pageSize):Response {

			$templatesQuery = $repository->findAllOrderByLikes($page, $pageSize);
			return $this->responseHelper->createReadResponse($templatesQuery);

		}

	}