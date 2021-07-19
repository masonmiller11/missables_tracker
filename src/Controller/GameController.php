<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\DTO\Transformer\ResponseTransformer\GameResponseDTOTransformer;
	use App\Repository\GameRepository;
	use App\Service\ResponseHelper;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
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

			try {

				/**
				 * @See GameResponseDTOTransformer
				 * @See ResponseHelper
				 */
				$game = $gameRepository->find($id);

				// $dto = $this->transformOne($game, $transformer);

				// return $this->responseHelper->createResponseForOne($dto);

				return new Response($serializer->serialize($game, 'json',[
					'circular_reference_handler' => function ($object) {
						return $object->getId();
					}
				]), Response::HTTP_OK, [
					'Content-Type' => 'application/json'
				]);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

		/**
		 * @Route(path="/create", methods={"POST"}, name="games.create")
		 *
		 * @param Request $request
		 * @param GameRequestDTOTransformer $transformer
		 * @return Response
		 */
		public function create(Request $request, GameRequestDTOTransformer $transformer): Response {

			$dto = $transformer->transformFromRequest($request);

			try {

				/**
				 * entityHelper->createGame validates gameRequestDTO before submitting to database. Returns Game entity
				 */
				$this->entityHelper->createGame($dto);
				return new JsonResponse([
					'status' => 'game created'
				],
					Response::HTTP_CREATED); //TODO add a Location Header for the URI of the resource

			} catch (\Exception $e) {

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
		public function search(GameResponseDTOTransformer $transformer,
		                       GameRepository $gameRepository): Response {

			try {

				$searchTerm = $this->request->getCurrentRequest()->query->get('game');
				$games = $gameRepository->searchByName($searchTerm);

				$dtos = $this->transformMany($games, $transformer);

				return $this->responseHelper->createResponseForMany($dtos);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

		/**
		 * @Route(path="/popular", methods={"GET"}, name="list_popular")
		 *
		 * @param GameRepository $gameRepository
		 * @param GameResponseDTOTransformer $transformer
		 * @return Response
		 *
		 * Queries the database for games, orders them by number of playthroughTemplates belonging and returns 10.
		 */
		public function listPopular(GameRepository $gameRepository,
									GameResponseDTOTransformer $transformer): Response {

			try {

				$games = $gameRepository->topTenByNumberOfTemplates();

				$dtos = $this->transformMany($games, $transformer);

				return $this->responseHelper->createResponseForMany($dtos);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

		/**
		 * @Route(path="/search/igdb", methods={"GET"}, name="search_igdb")
		 *
		 * @param SerializerInterface $serializer
		 * @return Response
		 * @throws TransportExceptionInterface
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 *
		 * This controller action looks at the URL query, searches IGDB and returns whatever IGDB sends us.
		 * So ?halo will return whatever IGDB poops up if we searched for Halo.
		 */
		public function searchIGDB(SerializerInterface $serializer): Response {

			try {

				$searchTerm = $this->request->getCurrentRequest()->query->get('game');

				/**
				 * This converts the response from IGDB to an array and returns it.
				 */
				$games = $this->IGDBHelper->searchIGDB($searchTerm);

				return new JsonResponse($games); //TODO what happens if we search for gibberish?

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}
		}

		/**
		 * @Route(path="/read/igdf/{internetGameDatabaseID<\d+>}", methods={"GET"}, name="get_game_from_igdb")
		 *
		 * @param string|int          $internetGameDatabaseID
		 * @return Response
		 * @throws TransportExceptionInterface
		 *
		 * Gets a game from IGDB. If it's already in our database, return Game entity from, if it's not in our database,
		 * create it with the data from IGDB and then return that new Game entity.
		 */
		public function getGameFromIGDB(string|int $internetGameDatabaseID, GameResponseDTOTransformer $transformer): Response {

			try{
				/**
				 * getGameAndSave uses the ID to retrieve data from IGDB, transforms it into a
				 * IGDBResponseDTO, then validates it before using that DTO to create a Game entity or to retrieve a
				 * Game entity from our database.
				 */
				$game = $this->IGDBHelper->getGameAndSave($internetGameDatabaseID);

				$dto = $this->transformOne($game, $transformer);

				return $this->responseHelper->createResponseForOne($dto);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

	}