<?php
	namespace App\Controller\Game;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\GameRepository;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class GameController
	 *
	 * @package App\Controller\Game
	 * @Route(path="/games/read", name="games.")
	 */
	final class ReadGameController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"GET"}, name="read")
		 *
		 * @param string|int     $id
		 * @param GameRepository $gameRepository
		 *
		 * @return Response
		 *
		 * Reads a single game from our database based on its id.
		 */
		public function read(string|int $id, GameRepository $gameRepository): Response {

			$game = $gameRepository->find($id);

			return $this->responseHelper->createReadResponse($game);

		}

	}