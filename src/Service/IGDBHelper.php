<?php
	namespace App\Service;

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

		public function __construct(HttpClientInterface $client, string $apiID, string $apiSecret) {

			$this->client = $client;
			$this->apiID = $apiID;
			$this->apiSecret = $apiSecret;

		}

		public function refreshToken (): ResponseInterface {

			return $this->client->request('POST	', 'https://id.twitch.tv/oauth2/token', [
				'query' => [
					'client_id' => $this->apiID,
					'client_secret' => $this->apiSecret,
					'grant_type' => 'client_credentials'
				],
			]);

		}

	}