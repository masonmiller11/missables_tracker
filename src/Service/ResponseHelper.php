<?php
	namespace App\Service;

	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;

	class ResponseHelper {

		/**
		 * @param Object|iterable|null $object
		 * @param SerializerInterface $serializer
		 * @param bool $contextFlag
		 * @return JsonResponse|Response
		 */
		public static function createReadResponse(object|iterable|null $object, SerializerInterface $serializer, bool $contextFlag = false): JsonResponse|Response {

			if (!$object || $object === []) {
				return new JsonResponse(['status' => 'error',
					'message' => 'resource not found'
				], Response::HTTP_NOT_FOUND);
			}

			return new Response($serializer->serialize($object, 'json', [
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}, 'context_flag' => $contextFlag,
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]);

		}

		/**
		 * @param string $uri
		 *
		 * @return JsonResponse
		 */
		public static function createResourceCreatedResponse(string $uri, string $id): JsonResponse {

			return new JsonResponse([
				'status' => 'resource created',
				"id" => $id
			], Response::HTTP_CREATED, [
				"Location" => $uri
			]);

		}

		/**
		 * @param string $uri
		 *
		 * @return JsonResponse
		 */
		public static function createResourceUpdatedResponse(string $uri): JsonResponse {

			return new JsonResponse([
				'status' => 'resource updated'
			], Response::HTTP_OK, [
//				"Location" => $uri
			]);

		}

		/**
		 * @return JsonResponse
		 */
		public static function createUserUpdatedResponse(): JsonResponse {

			return new JsonResponse([
				'status' => 'user updated'
			]);

		}

		/**
		 * @return JsonResponse
		 */
		public static function createLikeCreatedResponse(): JsonResponse {

			return new JsonResponse([
				'status' => 'like updated'
			]);

		}


		/**
		 * @return JsonResponse
		 */
		public static function createResourceDeletedResponse(): JsonResponse {

			return new JsonResponse([
				'status' => 'resource deleted'
			], Response::HTTP_OK, [
			]);

		}

		public static function createValidationErrorResponse(ValidationException $exception): JsonResponse {

			$errors = [];

			foreach ($exception->getViolations() as $error) {
				$errors[] = [
					'property' => $error->getPropertyPath(),
					'message' => $error->getMessage(),
				];
			}

			return self::createJsonErrorResponse($errors, 'validation error');

		}

		public static function createDuplicateResourceErrorResponse(string $errorMessage): JsonResponse {

			return self::createJsonErrorResponse($errorMessage, 'duplicate resource');

		}

		public static function createJsonErrorResponse (string|array $errorMessage, string $status): JsonResponse {
			return new JsonResponse([
				'status' => $status,
				'message' => $errorMessage
			], Response::HTTP_BAD_REQUEST);
		}

	}