<?php
	namespace App\Service;

	use App\DTO\IGDBResponseDTO;
	use App\DTO\Transformer\ResponseTransformer\IGDBResponseDTOTransformer;
	use App\Entity\Game;
	use App\Entity\IGDBConfig;
	use App\Utility\InternetGameDatabaseEndpoints;
	use App\Repository\GameRepository;
	use App\Repository\IGDBConfigRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\NonUniqueResultException;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
	use Symfony\Contracts\HttpClient\HttpClientInterface;
	use Symfony\Contracts\HttpClient\ResponseInterface;

	class IGDBHelper {

		/**
		 * @var string
		 */
		private string $apiSecret;

		/**
		 * @var string
		 */
		private string $apiID;

		/**
		 * @var HttpClientInterface
		 */
		private HttpClientInterface $client;

		/**
		 * @var IGDBConfigRepository
		 */
		private IGDBConfigRepository $IGDBConfigRepository;

		/**
		 * @var EntityManagerInterface
		 */
		private EntityManagerInterface $entityManager;

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var IGDBConfig|null
		 */
		private IGDBConfig|null $IGDBConfig;

		/**
		 * @var array
		 */
		private array $headers;

		/**
		 * @var IGDBResponseDTOTransformer
		 */
		private IGDBResponseDTOTransformer $IGDBResponseDTOTransformer;

		/**
		 * @var ValidatorInterface
		 */
		private ValidatorInterface $validator;

		public function __construct(HttpClientInterface $client,
		                            string $apiID,
		                            string $apiSecret,
		                            IGDBConfigRepository $IGDBConfigRepository,
		                            EntityManagerInterface $entityManager,
									GameRepository $gameRepository,
									ValidatorInterface $validator,
									IGDBResponseDTOTransformer $IGDBResponseDTOTransformer) {

			$this->client = $client;
			$this->apiID = $apiID;
			$this->apiSecret = $apiSecret;
			$this->IGDBConfigRepository = $IGDBConfigRepository;
			$this->IGDBResponseDTOTransformer = $IGDBResponseDTOTransformer;
			$this->gameRepository = $gameRepository;
			$this->entityManager = $entityManager;
			$this->IGDBConfig = $IGDBConfigRepository->find(1);
			$this->validator = $validator;


			$this->headers = [
				'Authorization' => 'Bearer ' . $this->IGDBConfig->getToken(),
				'Client-ID' => $this->apiID,
				'Content-Type' => 'text/plain'
			];

		}

		/**
		 * @throws TransportExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 */
		public function getToken (): array {

			$response = $this->client->request('POST', InternetGameDatabaseEndpoints::TOKEN, [
				'query' => [
					'client_id' => $this->apiID,
					'client_secret' => $this->apiSecret,
					'grant_type' => 'client_credentials'
				],
			]);

			return $response->toArray();

		}

		/**
		 * @throws \Exception
		 */
		public function refreshTokenInDatabase ($response): JsonResponse {

			$timeToExpirationInSeconds = $response["expires_in"];
			$now = new \DateTimeImmutable();
			$expiration = $now->add(new \DateInterval('PT' . $timeToExpirationInSeconds . 'S'));

			$currentConfig = $this->IGDBConfigRepository->find(1);

			if (!$currentConfig) {

				$config = new IGDBConfig($response["access_token"], $expiration,$now);

				$this->entityManager->persist($config);
				$this->entityManager->flush();

				return new JsonResponse(['status' => 'token created'], Response::HTTP_CREATED);

			} else {

				$currentConfig->setToken($response["access_token"]);
				$currentConfig->setGeneratedAt($now);
				$currentConfig->setExpiration($expiration);

				$this->entityManager->persist($currentConfig);
				$this->entityManager->flush();

				return new JsonResponse(['status' => 'token refreshed'], Response::HTTP_CREATED);

			}

		}

		/**
		 * @param int $ID
		 *
		 * @return IGDBResponseDTO|\RuntimeException
		 * @throws TransportExceptionInterface
		 */
		public function getGame (int $ID): IGDBResponseDTO | \RuntimeException {

			$response = $this->client->request( 'POST', InternetGameDatabaseEndpoints::GAMES, [
				'headers' => $this->headers,
				'body' => 'fields name, id, rating, summary, storyline, slug, screenshots, platforms, first_release_date,
				 cover, artworks; where id = ' . $ID . ';'
			]);

			return $this->IGDBResponseDTOTransformer->transformFromObject($response);

		}

		/**
		 * @throws NonUniqueResultException
		 */
		public function isIGDBGameInDatabase (IGDBResponseDTO $internetGameDatabaseDTO): Game | NonUniqueResultException | null {

			return $this->gameRepository->findGameByInternetGameDatabaseID($internetGameDatabaseDTO->id);

		}

		/**
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 */
		public function getGameAndSave (string|int $internetGameDatabaseID): Game {

			/**
			 * returns an IGDBResponseDTO with data from IGDB, does not touch our database
			 * @see IGDBResponseDTO
			 */
			$dto = $this->getGame($internetGameDatabaseID);

			/**
			 * Validate DTO, throw an error if not valid
			 */
			$errors = $this->validator->validate($dto);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new \RuntimeException($errorString);
			}

			/**
			 * Returns a Game entity if it's in database. Throws NonUniqueResultException if there's more than one entry.
			 * Returns null if it does not find Game.
			 * @see Game
			 */
			$gameIfInDatabase = $this->isIGDBGameInDatabase($dto);

			/**
			 * If $gameIfInDatabase is not present, then create a new Game entity and return it in response.
			 */
			if (!$gameIfInDatabase) {

				$game = new Game($dto->genre, $dto->title, $dto->id, $dto->screenshots, $dto->artworks, $dto->cover,
					$dto->platforms,$dto->slug, $dto->rating, $dto->summary, $dto->storyline,
					$dto->releaseDate
				);

				$this->entityManager->persist($game);
				$this->entityManager->flush();

				return $game;
			}

			/**
			 * Otherwise, return the Game entity in response.
			 */
			return $gameIfInDatabase;

		}

	}