<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
	use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	/**
	 * Class GameController
	 *
	 * @package App\Controller
	 * @Route(path="/games", name="games.")
	 */
	final class GameController extends AbstractBaseApiController {

		/**
		 * @Route(path="/read/{id<\d+>}", methods={"GET"}, name="read")
		 *
		 * @param string|int $id
		 * @param GameRepository $gameRepository
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 *
		 * Reads a single game from our database based on its id.
		 */
		public function read(string|int $id, GameRepository $gameRepository,
							 SerializerInterface $serializer): Response {

			$game = $gameRepository->find($id);

			return $this->responseHelper->createResponse($game);

		}

		/**
		 * @Route(path="/create", methods={"POST"}, name="games.create")
		 *
		 * @param Request $request
		 * @param GameRequestDTOTransformer $transformer
		 * @return Response
		 */
		public function create(Request $request, GameRequestDTOTransformer $transformer): Response {

			try {

				$dto = $this->transformOne($request, $transformer);

				$this->validate($dto);

				$game = $this->entityAssembler->createGame($dto);

				$this->entityManager->persist($game);
				$this->entityManager->flush();

				return $this->responseHelper->returnResourceCreatedResponse('games/read/' . $game->getId());

			} catch (ValidationException|\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

		/**
		 * @Route(path="/search", methods={"GET"}, name="search_game")
		 *
		 * @return Response
		 *
		 * Searches our database for title that includes the combination of letters in query.
		 * Such as ?game=halo returning Halo and Halo 4 or ?game=final returning Final Fantasy and Final Fight.
		 */
		public function search( GameRepository $gameRepository): Response {

				$searchTerm = $this->request->getCurrentRequest()->query->get('game');
				$games = $gameRepository->searchByName($searchTerm);

				return $this->responseHelper->createResponse($games);

		}

		/**
		 * @Route(path="/popular", methods={"GET"}, name="list_popular")
		 *
		 * @param GameRepository $gameRepository
		 * @return Response
		 *
		 * Queries the database for games, orders them by number of playthroughTemplates belonging and returns 10.
		 */
		public function listPopular(GameRepository $gameRepository): Response {

			try {

				$games = $gameRepository->topTenByNumberOfTemplates();

				return $this->responseHelper->createResponse($games);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

		/**
		 * @Route(path="/search/igdb", methods={"GET"}, name="search_igdb")
		 *
		 * @return Response
		 *
		 * This controller action looks at the URL query, searches IGDB and returns whatever IGDB sends us.
		 * So ?halo will return whatever IGDB poops up if we searched for Halo.
		 */
		public function searchIGDB(): Response {

			try {

				$searchTerm = $this->request->getCurrentRequest()->query->get('game');

				$games = $this->IGDBHelper->searchIGDB($searchTerm);

				if (!$games || $games === []) {
					throw new NotFoundHttpException();
				}

				return new JsonResponse($games);

			} catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface |
			TransportExceptionInterface | DecodingExceptionInterface $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

	}