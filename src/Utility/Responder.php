<?php
	namespace App\Utility;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\ResponseTransformer\ResponseDTOTransformerInterface;
	use App\Entity\EntityInterface;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Serializer;
	use Symfony\Component\Validator\Validation;

	class Responder {

		/**
		 * @param EntityInterface|iterable|null $object
		 * @param ResponseDTOTransformerInterface $transformer
		 * @return Response
		 */
		public static function createResponseFromObject (EntityInterface|iterable|null $object, ResponseDTOTransformerInterface $transformer): Response {

			if (is_iterable($object)) {

				$dto = $transformer->transformFromObjects($object);

				if ($object === []) {
					return new JsonResponse([
						'status' => 'error',
						'errors' => 'resources not found'
					],
						Response::HTTP_NOT_FOUND
					);
				}
			} else {

				$dto = $transformer->transformFromObject($object);

				if (!$object) {
					return new JsonResponse([
						'status' => 'error',
						'errors' => 'resource not found'
					],
						Response::HTTP_NOT_FOUND
					);
				}
			}

			$validator = Validation::createValidatorBuilder()->getValidator();

			$errors = $validator->validate($dto);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			}

			$normalizers = array(new ObjectNormalizer());
			$encoders = array(new JsonEncoder());
			$serializer = new Serializer($normalizers, $encoders);

			return new Response($serializer->serialize($dto, 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;

		}

	}