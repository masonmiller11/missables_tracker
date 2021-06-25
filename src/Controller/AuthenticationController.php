<?php
	namespace App\Controller;

	use App\Entity\User;
	use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\Routing\Annotation\Route;

	class AuthenticationController extends AbstractController {

		/**
		 * @Route(path="/login/refresh", methods={"GET"}, name="auth.login.refresh")
		 */

		public function refresh (JWTTokenManagerInterface $tokenManager): JsonResponse {

			$user = $this->getUser();

			assert($user instanceof User);

			return new JsonResponse([
				'token' => $tokenManager->create($user),
			]);
		}

	}