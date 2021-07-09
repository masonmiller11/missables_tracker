<?php
	namespace App\Service;

	use App\Entity\Game;
	use App\Entity\IGDBConfig;
	use App\Utility\InternetGameDatabaseEndpoints;
	use App\Repository\GameRepository;
	use App\Repository\IGDBConfigRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
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

		public function __construct(HttpClientInterface $client,
		                            string $apiID,
		                            string $apiSecret,
		                            IGDBConfigRepository $IGDBConfigRepository,
		                            EntityManagerInterface $entityManager,
									GameRepository $gameRepository) {

			$this->client = $client;
			$this->apiID = $apiID;
			$this->apiSecret = $apiSecret;
			$this->IGDBConfigRepository = $IGDBConfigRepository;
			$this->gameRepository = $gameRepository;
			$this->entityManager = $entityManager;
			$this->IGDBConfig = $IGDBConfigRepository->find(1);

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
		 * @throws TransportExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws ClientExceptionInterface
		 */
		public function getGame (int $ID): array {

			$response = $this->client->request( 'POST', InternetGameDatabaseEndpoints::GAMES, [
				'headers' => $this->headers,
				'body' => 'fields name, id, rating, summary, storyline, slug, screenshots, platforms, first_release_date,
				 cover, artworks; where id = ' . $ID . ';'
			]);

			return $response->toArray();

			//$game[0]["id"]
			//TODO create a responseDTO and return it here, and put type hint DTO below in the addGameToDatabase method below. Call both
			//TODO both of these methods in the game controller, specifically the getGameFromIGDB action.

		}

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

		public function addGameToDatabaseIfEmpty (int $internetGameDatabaseID) {

			$isGameInDatabase = $this->gameRepository->findGameByInternetGameDatabaseID($internetGameDatabaseID);

			if ($isGameInDatabase) {
				return; //TODO return some sort of success message
			}

	}

	}