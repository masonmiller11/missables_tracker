<?php
	namespace App\Service;

	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
	use Symfony\Component\Serializer\Serializer;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Constraints\Valid;
	use Symfony\Component\Validator\Exception\ValidationFailedException;

	class ResponseHelper {

		/**
		 * @param Object|iterable|null $object
		 * @param SerializerInterface $serializer
		 * @return iterable|JsonResponse|Response
		 */
		public static function createReadResponse (Object|iterable|null $object, SerializerInterface $serializer): iterable|JsonResponse|Response {

			if (!$object || $object === []) {
				throw new NotFoundHttpException();
			}

			return new Response($serializer->serialize($object, 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]);

		}

		/**
		 * @param string $uri
		 *
		 * @return JsonResponse
		 */
		public static function createResourceCreatedResponse (string $uri): JsonResponse {

			return new JsonResponse([
					'status' => 'resource created'
				], Response::HTTP_CREATED, [
					"Location" => $uri
				]);

		}

		/**
		 * @param string $uri
		 *
		 * @return JsonResponse
		 */
		public static function createResourceUpdatedResponse (string $uri): JsonResponse {

			return new JsonResponse([
				'status' => 'resource updated'
			], Response::HTTP_OK, [
//				"Location" => $uri
			]);

		}

		/**
		 * @return JsonResponse
		 */
		public static function createUserUpdatedResponse (): JsonResponse {

			return new JsonResponse([
				'status' => 'user updated'
			]);

		}

		/**
		 * @return JsonResponse
		 */
		public static function createLikeCreatedResponse (): JsonResponse {

			return new JsonResponse([
				'status' => 'like updated'
			]);

		}


		/**
		 * @return JsonResponse
		 */
		public static function createResourceDeletedResponse (): JsonResponse {

			return new JsonResponse([
				'status' => 'resource deleted'
			], Response::HTTP_OK, [
			]);

		}

		public static function createValidationErrorResponse (ValidationFailedException $exception): JsonResponse {

			$errors = [];
			
			foreach ($exception->getViolations() as $error) {
				$errors[] = $error->getMessage();
			}

			return new JsonResponse([
				'status' => 'validation error',
				"message" => $errors
			], Response::HTTP_BAD_REQUEST);

		}

		public static function createDuplicateResourceErrorResponse (string $errorMessage): JsonResponse {

			return new JsonResponse([
				'status' => 'duplicate resource',
				"message" => $errorMessage
			], Response::HTTP_BAD_REQUEST);

		}

	}