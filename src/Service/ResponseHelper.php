<?php
	namespace App\Service;

	use App\DTO\Exception\ValidationException;
	use App\DTO\Transformer\ResponseTransformer\ResponseDTOTransformerInterface;
	use App\Entity\EntityInterface;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Exception\ResourceNotFoundException;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class ResponseHelper {

		/**
		 * @var ValidatorInterface
		 */
		private ValidatorInterface $validator;

		/**
		 * @var SerializerInterface
		 */
		private SerializerInterface $serializer;

		public function __construct(ValidatorInterface $validator,
		                            SerializerInterface $serializer) {

			$this->validator = $validator;
			$this->serializer = $serializer;

		}

		/**
		 * @param EntityInterface|iterable $objects
		 * @param ResponseDTOTransformerInterface $transformer
		 *
		 * @return iterable|JsonResponse|Response
		 * @throws ResourceNotFoundException
		 */
		public function createResponseForMany (iterable $objects,
		                                       ResponseDTOTransformerInterface $transformer): iterable|JsonResponse|Response {

			if ($objects === []) {
				throw new ResourceNotFoundException('resource not found');
			}

			$dto = $transformer->transformFromObjects($objects);

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

			return new Response($this->serializer->serialize($dto, 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;

		}

		/**
		 * @param EntityInterface                 $object
		 * @param ResponseDTOTransformerInterface $transformer
		 *
		 * @return iterable|JsonResponse|Response
		 * @throws ResourceNotFoundException
		 */
		public function createResponseForOne (EntityInterface $object,
		                                      ResponseDTOTransformerInterface $transformer): iterable|JsonResponse|Response {

			if (!$object) {
				throw new ResourceNotFoundException('resource not found');
			}

			$dto = $transformer->transformFromObject($object);

			$errors = $this->validator->validate($dto);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

			return new Response($this->serializer->serialize($dto, 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]);

		}

		/**
		 * @param \Exception $exception
		 * @return JsonResponse
		 */
		public function createErrorResponse (\Exception $exception): JsonResponse {

			return new JsonResponse(['status' => 'error','code' => $exception->getCode(),
				'message' => $exception->getMessage(), 'file' => $exception->getFile()], Response::HTTP_INTERNAL_SERVER_ERROR);

		}

	}