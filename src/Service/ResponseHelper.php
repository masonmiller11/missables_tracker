<?php
	namespace App\Service;

	use App\Entity\EntityInterface;
	use App\Exception\ValidationException;
	use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Exception\ValidationFailedException;

	class ResponseHelper {

		/**
		 * @var SerializerInterface
		 */
		private SerializerInterface $serializer;

		public function __construct(SerializerInterface $serializer) {

			$this->serializer = $serializer;

		}

		/**
		 * @param Object|iterable $object
		 *
		 * @return iterable|JsonResponse|Response
		 */
		public function createResponse (Object|iterable $object): iterable|JsonResponse|Response {

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
		public function returnResourceCreatedResponse (string $uri): JsonResponse {

			return new JsonResponse([
					'status' => 'resource created'
				], Response::HTTP_CREATED, [
					"Location" => $uri
				]);

		}

		/**
		 * @param \Exception $exception
		 * @return JsonResponse
		 */
		public function createErrorResponse (\Exception $exception): JsonResponse {

			if ($exception instanceof UniqueConstraintViolationException) {
				return new JsonResponse(['status' => 'error',
					'message' => 'duplicate resource'], Response::HTTP_CONFLICT);
			}

			if ($exception instanceof ValidationException) {
				return new JsonResponse(['status' => 'error',
					'message' => 'validation failed'], Response::HTTP_BAD_REQUEST);
			}

			return new JsonResponse(['status' => 'error','code' => $exception->getCode(),
				'message' => $exception->getMessage(), 'file' => $exception->getFile()], Response::HTTP_INTERNAL_SERVER_ERROR);

		}

	}