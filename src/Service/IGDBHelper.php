<?php
	namespace App\Service;

	use App\DTO\Game\IGDBGameResponseDTO;
	use App\DTO\Transformer\ResponseTransformer\IGDBGameResponseDTOTransformer;
	use App\Entity\IGDBConfig;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
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
		 * @param GameRepository $gameRepository
		 * @param ValidatorInterface $validator
		 * @param IGDBGameResponseDTOTransformer $IGDBGameResponseDTOTransformer
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
		) {

			$this->client = $client;
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
			$diff = (new DateTimeImmutable('now'))->diff($this->IGDBConfig->getExpiration());

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

			$games = $response->toArray();

			$getArtworkURI = function ($game) {

				if (isset($game['cover'])) $game['cover'] = $this->getCoverArtWorkURIFromIGDB($game['cover']);

				return $game;
			};

			return array_map($getArtworkURI, $games);

		}

		/**
		 * @param string $ID
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

			$response = $response->toArray()[0] ?? 'unavailable';

			$imageId = $response['image_id'] ?? 'unavailable';

			return $uri = 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $imageId . '.jpg';

		}

		/**
		 * @param int $ID
		 *
		 * @return IGDBGameResponseDTO|RuntimeException
		 * @throws TransportExceptionInterface
		 * @throws Exception
		 */
		public function getGameFromIGDB(int $ID): IGDBGameResponseDTO|RuntimeException {

			$response = $this->client->request('POST', InternetGameDatabaseEndpoints::GAMES, [
				'headers' => $this->headers,
				'body' => 'fields name, id, rating, summary, storyline, slug, screenshots, platforms, genres, 
				first_release_date, cover, artworks; where id = ' . $ID . ';'
			]);

			//TODO eventually we want to save the cover's URL so we aren't constantly pinging IGDB
			$dto = $this->IGDBGameResponseDTOTransformer->transformFromObject($response);

			$this->validateDTO($dto);

			return $dto;

		}

		/**
		 * @throws ValidationException
		 */
		private function validateDTO(IGDBGameResponseDTO $dto): void {
			$errors = $this->validator->validate($dto);
			if (count($errors) > 0)
				throw new ValidationException($errors);
		}

	}