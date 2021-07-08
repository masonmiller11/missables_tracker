<?php
	namespace App\Controller;

	use App\Service\IGDBHelper;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;

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
			$response = $this->IGDBHelper->refreshToken();

			return new JsonResponse($response);

		}

	}