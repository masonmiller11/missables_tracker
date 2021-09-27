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

			try {

				$this->doUpdate($request, $this->getUser()->getId());

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createUserUpdatedResponse();
		}

	}