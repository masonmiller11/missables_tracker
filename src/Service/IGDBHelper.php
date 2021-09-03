<?php
	namespace App\Service;

	use App\DTO\Game\IGDBGameResponseDTO;
	use App\DTO\Transformer\ResponseTransformer\IGDBGameResponseDTOTransformer;
	use App\Entity\Game;
	use App\Entity\IGDBConfig;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
	use App\Repository\IGDBConfigRepository;
	use App\Transformer\GameEntityTransformer;
	use App\Utility\InternetGameDatabaseEndpoints;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\NonUniqueResultException;
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
		 * @var IGDBGameResponseDTOTransformer
		 */
		private IGDBGameResponseDTOTransformer $IGDBGameResponseDTOTransformer;

		/**
		 * @var ValidatorInterface
		 */
		private ValidatorInterface $validator;

		/**
		 * @var GameEntityTransformer
		 */
		private GameEntityTransformer $entityTransformer;

		/**
		 * @param HttpClientInterface $client
		 * @param string $apiID
		 * @param string $apiSecret
		 * @param IGDBConfigRepository $IGDBConfigRepository
		 * @param EntityManagerInterface $entityManager
		 * @param GameRepository $gameRepository
		 * @param ValidatorInterface $validator
		 * @param IGDBGameResponseDTOTransformer $IGDBGameResponseDTOTransformer
		 * @param GameEntityTransformer $entityTransformer
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		public function __construct(HttpClientInterface $client,
		                            string $apiID,
		                            string $apiSecret,
		                            IGDBConfigRepository $IGDBConfigRepository,
		                            EntityManagerInterface $entityManager,
		                            GameRepository $gameRepository,
		                            ValidatorInterface $validator,
		                            IGDBGameResponseDTOTransformer $IGDBGameResponseDTOTransformer,
		                            GameEntityTransformer $entityTransformer) {

			$this->client = $client;
			$this->entityTransformer = $entityTransformer;
			$this->IGDBGameResponseDTOTransformer = $IGDBGameResponseDTOTransformer;
			$this->gameRepository = $gameRepository;
			$this->entityManager = $entityManager;
			$this->validator = $validator;

			/**
			 * $apiID and $apiSecret are bound in services.yaml to environment variables
			 */
			$this->apiID = $apiID;
			$this->apiSecret = $apiSecret;


			$this->IGDBConfigRepository = $IGDBConfigRepository;
			$this->IGDBConfig = $IGDBConfigRepository->find(1);

			if (!$this->IGDBConfig) {
				$this->IGDBConfig = $this->refreshTokenInDatabase();
			}

			// $diff is time until expiration.
			$diff = (new \DateTimeImmutable('now'))->diff($this->IGDBConfig->getExpiration());

			//If the token expires sometime within the next day, let's refresh it.
			if (!$diff->days > 1) {
				$this->IGDBConfig = $this->refreshTokenInDatabase();
			}

			$this->headers = [
				'Authorization' => 'Bearer ' . $this->IGDBConfig->getToken(),
				'Client-ID' => $this->apiID,
				'Content-Type' => 'text/plain'
			];

		}

		/**
		 * @return IGDBConfig
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 */
		public function refreshTokenInDatabase(): IGDBConfig {


			$tokenResponse = $this->getToken();

			$timeToExpirationInSeconds = $tokenResponse["expires_in"];
			$now = new \DateTimeImmutable();
			$expiration = $now->add(new \DateInterval('PT' . $timeToExpirationInSeconds . 'S'));

			$currentConfig = $this->IGDBConfigRepository->find(1);

			if (!$currentConfig) {

				$config = new IGDBConfig($tokenResponse["access_token"], $expiration, $now);

				$this->entityManager->persist($config);
				$this->entityManager->flush();

				return $config;

			}

			$currentConfig->setToken($tokenResponse["access_token"]);
			$currentConfig->setGeneratedAt($now);
			$currentConfig->setExpiration($expiration);

			$this->entityManager->persist($currentConfig);
			$this->entityManager->flush();

			return $currentConfig;

		}

		/**
		 * @throws TransportExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 */
		private function getToken(): array {

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
		 * @throws TransportExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws ClientExceptionInterface
		 */
		public function searchIGDB(string $term, int $limit = 20): array {

			$response = $this->client->request(
				'POST',
				InternetGameDatabaseEndpoints::GAMES,
				[
					'headers' => $this->headers,
					'body' => 'fields name, id, cover, platforms, summary, first_release_date;
			search "' . $term . '";
			where version_parent = null;
			limit ' . $limit . ';'
				]
			);

			return $response->toArray();

		}

		/**
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 * @throws DecodingExceptionInterface
		 *
		 * This gets a game based on IGDB id. First it tries to get it from our database.
		 * If the game isn't already in our database, it will get it from IGDB and add it.
		 */
		public function getGameAndSave(string|int $internetGameDatabaseID): Game {

			/**
			 * returns an IGDBResponseDTO with data from IGDB, does not touch our database
			 * @see IGDBGameResponseDTO
			 */
			$dto = $this->getGameFromIGDB($internetGameDatabaseID);


			$errors = $this->validator->validate($dto);
			if (count($errors) > 0)
				throw new ValidationException($errors);

			/**
			 * Returns a Game entity if it's in database. Throws NonUniqueResultException if there's more than one entry.
			 * Returns null if it does not find Game.
			 * @see Game
			 */
			$gameIfInDatabase = $this->getIGDBGameIfInDatabase($dto);

			/**
			 * If $gameIfInDatabase is not present, then create a new Game entity and return it in response.
			 */
			if (!$gameIfInDatabase) {

				$game = $this->entityTransformer->assemble($dto);

				$artwork = new GameCoverArt($this->getCoverArtWorkURIFromIGDB($game->getId()), $game);

				return $game;
			}

			/**
			 * Otherwise, return the Game entity in response.
			 */
			return $gameIfInDatabase;

		}

		/**
		 * @param int $ID
		 *
		 * @return IGDBGameResponseDTO|\RuntimeException
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 */
		private function getGameFromIGDB(int $ID): IGDBGameResponseDTO|\RuntimeException {

			$response = $this->client->request('POST', InternetGameDatabaseEndpoints::GAMES, [
				'headers' => $this->headers,
				'body' => 'fields name, id, rating, summary, storyline, slug, screenshots, platforms, first_release_date,
				 cover, artworks; where id = ' . $ID . ';'
			]);

			return $this->IGDBGameResponseDTOTransformer->transformFromObject($response);

		}

		/**
		 * @param int $ID
		 *
		 * @return string
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		public function getCoverArtWorkURIFromIGDB(string $ID): string {

			$response = $this->client->request('POST', InternetGameDatabaseEndpoints::COVER, [
				'headers' => $this->headers,
				'body' => 'fields *; where id = ' . $ID . ';'
			]);

			$response = $response->toArray()[0];

			$imageId = $response['image_id'];

			return $uri = 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $imageId .    '.jpg';

		}

		/**
		 * @throws NonUniqueResultException
		 */
		private function getIGDBGameIfInDatabase(IGDBGameResponseDTO $internetGameDatabaseDTO): Game|NonUniqueResultException|null {

			return $this->gameRepository->findGameByInternetGameDatabaseID($internetGameDatabaseDTO->internetGameDatabaseID);

		}

	}