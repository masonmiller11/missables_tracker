<?php
	namespace App\Controller\Game;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\GameRepository;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class GameController
	 *
	 * @package App\Controller
	 * @Route(path="/games", name="games.list_")
	 */
	final class ListGamesController extends AbstractBaseApiController {

		/**
		 * @Route(path="/popular", methods={"GET"}, name="popular")
		 *
		 * @param GameRepository $gameRepository
		 * @return Response
		 *
		 * Queries the database for games, orders them by number of playthroughTemplates belonging and returns 10.
		 */
		public function listPopular(GameRepository $gameRepository): Response {

			$games = $gameRepository->topTenByNumberOfTemplates();

			return $this->responseHelper->createResponse($games);

		}

	}