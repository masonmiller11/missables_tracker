<?php
	namespace App\Service;

	use App\DTO\Transformer\ResponseTransformer\ResponseDTOTransformerInterface;
	use App\Entity\EntityInterface;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
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
		 * @return iterable|JsonResponse|Response
		 */
		public function validateAndTransformMany (iterable $objects,
		                                          ResponseDTOTransformerInterface $transformer): iterable|JsonResponse|Response {

			if ($objects === []) {
				return new JsonResponse([
					'status' => 'error',
					'errors' => 'resources not found'
				],
					Response::HTTP_NOT_FOUND
				);
			}

			$dto = $transformer->transformFromObjects($objects);

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			} else {
				return new Response($this->serializer->serialize($dto, 'json',[
					'circular_reference_handler' => function ($object) {
						return $object->getId();
					}
				]), Response::HTTP_OK, [
					'Content-Type' => 'application/json'
				]) ;
			}

		}

		/**
		 * @param EntityInterface $object
		 * @param ResponseDTOTransformerInterface $transformer
		 * @return iterable|JsonResponse|Response
		 */
		public function validateAndTransformOne (EntityInterface $object,
		                                          ResponseDTOTransformerInterface $transformer): iterable|JsonResponse|Response {

			if (!$object) {
				return new JsonResponse([
					'status' => 'error',
					'errors' => 'resource not found'
				],
					Response::HTTP_NOT_FOUND
				);
			}

			$dto = $transformer->transformFromObject($object);

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			} else {
				return new Response($this->serializer->serialize($dto, 'json',[
					'circular_reference_handler' => function ($object) {
						return $object->getId();
					}
				]), Response::HTTP_OK, [
					'Content-Type' => 'application/json'
				]) ;
			}

		}

	}