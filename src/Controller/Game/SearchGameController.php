<?php
	namespace App\Controller\Game;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\GameRepository;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	/**
	 * Class GameController
	 *
	 * @package App\Controller
	 * @Route(path="/games/search", name="games.")
	 */
	final class SearchGameController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"GET"}, name="search_game")
		 *
		 * @return Response
		 *
		 * Searches our database for title that includes the combination of letters in query.
		 * Such as ?game=halo returning Halo and Halo 4 or ?game=final returning Final Fantasy and Final Fight.
		 */
		public function search( GameRepository $gameRepository): Response {

			$searchTerm = $this->request->getCurrentRequest()->query->get('game');
			$games = $gameRepository->searchByName($searchTerm);

			return $this->responseHelper->createReadResponse($games);

		}

		/**
		 * @Route(path="/igdb", methods={"GET"}, name="search_igdb")
		 *
		 * @return Response
		 *
		 * This controller action looks at the URL query, searches IGDB and returns whatever IGDB sends us.
		 * So ?halo will return whatever IGDB finds after searching for halo
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		public function searchIGDB(): Response {

			$searchTerm = $this->request->getCurrentRequest()->query->get('game');

			$games = $this->IGDBHelper->searchIGDB($searchTerm);

			if (!$games || $games === []) {
				throw new NotFoundHttpException();
			}

			return new JsonResponse($games);

		}

	}