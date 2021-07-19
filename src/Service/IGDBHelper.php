<?php
	namespace App\Service;

	use App\Exception\ValidationException;
	use App\DTO\IGDBGameResponseDTO;
	use App\DTO\Transformer\ResponseTransformer\IGDBGameResponseDTOTransformer;
	use App\Entity\Game;
	use App\Entity\IGDBConfig;
	use App\Utility\InternetGameDatabaseEndpoints;
	use App\Repository\GameRepository;
	use App\Repository\IGDBConfigRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\NonUniqueResultException;
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
		 * @var EntityAssembler
		 */
		private EntityAssembler $entityAssembler;

		/**
		 * @throws \Exception
		 */
		public function __construct(HttpClientInterface $client,
		                            string $apiID,
		                            string $apiSecret,
		                            IGDBConfigRepository $IGDBConfigRepository,
		                            EntityManagerInterface $entityManager,
		                            GameRepository $gameRepository,
		                            ValidatorInterface $validator,
		                            IGDBGameResponseDTOTransformer $IGDBGameResponseDTOTransformer,
									EntityAssembler $entityAssembler) {

			$this->client = $client;

			/**
			 * $apiID and $apiSecret are bound in services.yaml to environment variables
			 */
			$this->apiID = $apiID;
			$this->apiSecret = $apiSecret;

			$this->entityAssembler = $entityAssembler;
			$this->IGDBConfigRepository = $IGDBConfigRepository;
			$this->IGDBGameResponseDTOTransformer = $IGDBGameResponseDTOTransformer;
			$this->gameRepository = $gameRepository;
			$this->entityManager = $entityManager;
			$this->validator = $validator;
			$this->IGDBConfig = $IGDBConfigRepository->find(1);

			if (!$this->IGDBConfig) {
				$this->IGDBConfig =$this->refreshTokenInDatabase();
			}

			// $diff is time until expiration.
			$diff = (new \DateTimeImmutable('now'))->diff($this->IGDBConfig->getExpiration());

			//If the token expires sometime within the next day, let's refresh it.
			if (!$diff->days >1) {
				$this->IGDBConfig = $this->refreshTokenInDatabase();
			}

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
		private function getToken (): array {

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
		 *
		 * Creates token if it ins't already in database, otherwise refreshes it.
		 */
		public function refreshTokenInDatabase (): IGDBConfig {

			try {
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


			} catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface
					 | ServerExceptionInterface | TransportExceptionInterface $e) {

				throw new \Exception($e);

			}

		}

		/**
		 * @throws TransportExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws ClientExceptionInterface
		 */
		public function searchIGDB (string $term, int $limit = 20): array{

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
		 * @param int $ID
		 *
		 * @return IGDBGameResponseDTO|\RuntimeException
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 */
		private function getGame (int $ID): IGDBGameResponseDTO | \RuntimeException {

			$response = $this->client->request( 'POST', InternetGameDatabaseEndpoints::GAMES, [
				'headers' => $this->headers,
				'body' => 'fields name, id, rating, summary, storyline, slug, screenshots, platforms, first_release_date,
				 cover, artworks; where id = ' . $ID . ';'
			]);

			return $this->IGDBGameResponseDTOTransformer->transformFromObject($response);

		}

		/**
		 * @throws NonUniqueResultException
		 */
		private function isIGDBGameInDatabase (IGDBGameResponseDTO $internetGameDatabaseDTO): Game | NonUniqueResultException | null {

			return $this->gameRepository->findGameByInternetGameDatabaseID($internetGameDatabaseDTO->internetGameDatabaseID);

		}

		/**
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 */
		public function getGameAndSave (string|int $internetGameDatabaseID): Game {

			/**
			 * returns an IGDBResponseDTO with data from IGDB, does not touch our database
			 * @see IGDBGameResponseDTO
			 */
			$dto = $this->getGame($internetGameDatabaseID);

			/**
			 * Validate DTO, throw an error if not valid
			 */
			$errors = $this->validator->validate($dto);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
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

				$game = $this->entityAssembler->createGame($dto);

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