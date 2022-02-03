<?php
	namespace App\Service;

	use App\DTO\Game\IGDBGameResponseDTO;
	use App\DTO\Transformer\ResponseTransformer\IGDBGameResponseDTOTransformer;
	use App\Entity\Game;
	use App\Entity\IGDBConfig;
	use App\Exception\ValidationException;
	use App\Repository\IGDBConfigRepository;
	use App\Utility\InternetGameDatabaseEndpoints;
	use DateInterval;
	use DateTimeImmutable;
	use Doctrine\ORM\EntityManagerInterface;
	use Exception;
	use RuntimeException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
	use Symfony\Contracts\HttpClient\HttpClientInterface;

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
		 * @param HttpClientInterface $client
		 * @param string $apiID
		 * @param string $apiSecret
		 * @param IGDBConfigRepository $IGDBConfigRepository
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param IGDBGameResponseDTOTransformer $IGDBGameResponseDTOTransformer
		 *
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		public function __construct(
			HttpClientInterface $client,
			string $apiID,
			string $apiSecret,
			IGDBConfigRepository $IGDBConfigRepository,
			EntityManagerInterface $entityManager,
			ValidatorInterface $validator,
			IGDBGameResponseDTOTransformer $IGDBGameResponseDTOTransformer,
		) {

			$this->client = $client;
			$this->IGDBGameResponseDTOTransformer = $IGDBGameResponseDTOTransformer;
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
			$diff = (new DateTimeImmutable('now'))->diff($this->IGDBConfig->getExpiration());

			//If the token expires sometime within the next day, let's refresh it.
			if (!$diff->days > 1) {
				$this->IGDBConfig = $this->refreshTokenInDatabase();
			}

			$this->headers = [
				'Authorization' => 'Bearer ' . $this->IGDBConfig->getToken(),
				'Client-ID' => $this->apiID,
				'Content-Type' => 'text/plain',
			];

		}

		/**
		 * @return IGDBConfig
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 * @throws Exception
		 */
		public function refreshTokenInDatabase(): IGDBConfig {

			$tokenResponse = $this->getToken();

			$timeToExpirationInSeconds = $tokenResponse["expires_in"];
			$now = new DateTimeImmutable();
			$expiration = $now->add(new DateInterval('PT' . $timeToExpirationInSeconds . 'S'));

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

			$response = $this->client->request(
				'POST',
				InternetGameDatabaseEndpoints::TOKEN,
				[
					'query' => [
						'client_id' => $this->apiID,
						'client_secret' => $this->apiSecret,
						'grant_type' => 'client_credentials',
					],
				]
			);

			return $response->toArray();

		}

		/**
		 * @throws TransportExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws ClientExceptionInterface
		 * @throws Exception
		 */
		public function searchIGDB(string $term, int $limit = 100): array {

			$response = $this->client->request(
				'POST',
				InternetGameDatabaseEndpoints::GAMES,
				[
					'headers' => $this->headers,
					'body' => 'fields name, id, rating, summary, storyline, slug, screenshots, platforms, genres, 
						first_release_date, cover, artworks;
						search "' . $term . '";
						where version_parent = null;
						limit ' . $limit . ';',
				]
			);

			$gamesData = $response->toArray();

			//Only return search results that have cover art and a summary.
			$gamesData = array_filter(
				$gamesData,
				fn($game) => isset($game['cover'], $game['summary'], $game['first_release_date'])
			);

			$gameDtos = [];

			foreach ($gamesData as $gameArray) {

				$dto = $this->IGDBGameResponseDTOTransformer->transformFromObject($gameArray);

				$this->validateDTO($dto);

				$gameDtos[] = $dto;

			}

			return $gameDtos;

		}

		/**
		 * @throws ValidationException
		 */
		private function validateDTO(IGDBGameResponseDTO $dto): void {
			$errors = $this->validator->validate($dto);
			if (count($errors) > 0)
				throw new ValidationException($errors);
		}

		/**
		 * @param Game $game
		 *
		 * @return string
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		public function getCoverArtForGame(Game $game): string {

			try {

				//Get the imageUri based off of the saved cover art id that IGDB originally gave us.
				return $this->getCoverArtImageUriFromIgdb($game->getCover());

			} catch (\ErrorException) {

				//Sometimes IGDB will change the cover art id, causing this to fail.
				//If it fails, let's try getting a new cover art id from IGDB and trying again.
				$igdbDTO = $this->getIgdbGameDto($game->getInternetGameDatabaseID());

				//Since we now know the current cover art id is out of date, let's save the new one.
				$this->saveNewCoverArtId($game, $igdbDTO->cover);

				return $this->getCoverArtImageUriFromIgdb($igdbDTO->cover);

			}

		}

		/**
		 * @param string $coverArtId
		 * @return string
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		private function getCoverArtImageUriFromIgdb(string $coverArtId): string {

			$response = $this->client->request('POST', InternetGameDatabaseEndpoints::COVER, [
				'headers' => $this->headers,
				'body' => 'fields *; where id = ' . $coverArtId . ';'
			]);

			$response = $response->toArray()[0];

			$imageId = $response['image_id'];

			return 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $imageId . '.jpg';

		}

		/**
		 * @param int $igdbId
		 *
		 * @return IGDBGameResponseDTO|RuntimeException
		 * @throws TransportExceptionInterface
		 * @throws Exception
		 */
		public function getIgdbGameDto(int $igdbId): IGDBGameResponseDTO|RuntimeException {

			$response = $this->client->request('POST', InternetGameDatabaseEndpoints::GAMES, [
				'headers' => $this->headers,
				'body' => 'fields name, id, rating, summary, storyline, slug, screenshots, platforms, genres, 
				first_release_date, cover, artworks; where id = ' . $igdbId . ';'
			]);

			//TODO eventually we want to save the cover's URL so we aren't constantly pinging IGDB
			$dto = $this->IGDBGameResponseDTOTransformer->transformFromObject($response);

			$this->validateDTO($dto);

			return $dto;

		}

		/**
		 * @param Game $game
		 * @param int $coverArtId
		 * @param string $coverArtUri
		 */
		private function saveNewCoverArt(Game $game, int $coverArtId, string $coverArtUri): void {

			$game->setCover($coverArtId);
			$game->setCoverUri($coverArtUri);

			$this->entityManager->persist($game);
			$this->entityManager->flush();

		}

	}