<?php
	namespace App\Controller;

	use App\Service\IGDBHelper;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface as ClientExceptionInterfaceAlias;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	/**
	 * Class IGDBController
	 *
	 * @package App\Controller
	 * @Route(path="/igdb", name="igdb.")
	 */
	class IGDBController extends AbstractController {

		private IGDBHelper $IGDBHelper;

		public function __construct(IGDBHelper $IGDBHelper) {
			$this->IGDBHelper = $IGDBHelper;
		}

		/**
		 * @Route(path="/refresh", methods={"GET"}, name="refresh_token")
		 *
		 * @return JsonResponse
		 */
		public function refreshToken(): JsonResponse {

			try {

				$response = $this->IGDBHelper->getToken();
				return $this->IGDBHelper->refreshTokenInDatabase($response);

			} catch (ClientExceptionInterfaceAlias | DecodingExceptionInterface |
				RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {

				return new JsonResponse(['status' => 'error', 'errors' => $e], Response::HTTP_INTERNAL_SERVER_ERROR);

			}

		}

	}