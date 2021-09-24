<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\UserRequestDTOTransformer;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\UserRepository;
	use App\Request\Payloads\UserPayload;
	use App\Service\ResponseHelper;
	use App\Transformer\UserEntityTransformer;
	use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 *
	 * @Route( name="user.")
	 */
	final class UserController extends AbstractBaseApiController {

		/**
		 * UserController constructor.
		 * @param ValidatorInterface $validator
		 * @param UserEntityTransformer $entityTransformer
		 * @param UserRequestDTOTransformer $DTOTransformer
		 * @param UserRepository $repository
		 * @param PayloadDecoderRegistryInterface $decoderRegistry
		 */
		public function __construct(
			ValidatorInterface $validator, UserEntityTransformer $entityTransformer,
			UserRequestDTOTransformer $DTOTransformer,
			UserRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {

			parent::__construct($validator,
				$entityTransformer,
				$DTOTransformer,
				$repository,
				$decoderRegistry->getDecoder(UserPayload::class)
			);

		}

		/**
		 * @Route(path="/signup", methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function create(Request $request): Response {

			try {

				$user = $this->doCreate($request);

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			} catch (UniqueConstraintViolationException $exception) {

				return ResponseHelper::createDuplicateResourceErrorResponse(
					'A user with that name or email already exists'
				);

			}

			return ResponseHelper::createResourceCreatedResponse('users/read' . $user->getId());

		}

		/**
		 * @Route(path="/user/update", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param null $id
		 *
		 * @return Response
		 */
		public function update(Request $request, $id = null): Response {

			$userId = $this->getUser()->getId();

			try {

				$this->entityTransformer->update($userId, $request);

			} catch (ValidationException $exception) {

				ResponseHelper::createValidationErrorResponse($exception);
			}

			return ResponseHelper::createUserUpdatedResponse();
		}

		/**
		 * @Route(path="user/update/password", methods={"PATCH"}, name="update_password")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function updatePassword(Request $request): Response {

			$data = json_decode($request->getContent(), true);

			if (!isset($data['password'])) {

				return ResponseHelper::createJsonErrorResponse('json must include password', 'validation error');

			}

			$password = $data['password'];

			$userId = $this->getUser()->getId();

			if (!$this->entityTransformer instanceof UserEntityTransformer)
				throw new \InvalidArgumentException(
					'entityTransformer is not have type UserEntityTransformer'
				);

			try {

				$this->entityTransformer->updatePassword($userId, $password);

			} catch (ValidationException $exception) {

				ResponseHelper::createValidationErrorResponse($exception);

			}

			return ResponseHelper::createUserUpdatedResponse();
		}

		protected function delete(int $id): Response {
			// TODO: Implement delete() method.
		}

		protected function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}

	}