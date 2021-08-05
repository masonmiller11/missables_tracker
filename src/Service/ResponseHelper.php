<?php
	namespace App\Service;

	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Serializer\SerializerInterface;

	class ResponseHelper {

		/**
		 * @var SerializerInterface
		 */
		private SerializerInterface $serializer;

		public function __construct(SerializerInterface $serializer) {

			$this->serializer = $serializer;

		}

		/**
		 * @param Object|iterable|null $object
		 *
		 * @return iterable|JsonResponse|Response
		 */
		public function createReadResponse (Object|iterable|null $object): iterable|JsonResponse|Response {

			if (!$object || $object === []) {
				throw new NotFoundHttpException();
			}

			return new Response($this->serializer->serialize($object, 'json',[
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
		public function createResourceCreatedResponse (string $uri): JsonResponse {

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
		public function createResourceUpdatedResponse (string $uri): JsonResponse {

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
		public function createResourceDeletedResponse (): JsonResponse {

			return new JsonResponse([
				'status' => 'resource deleted'
			], Response::HTTP_OK, [
			]);

		}

	}