<?php

	namespace App\Controller;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\RequestTransformer\RequestDTOTransformerInterface;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\DecoderIntent;
	use App\Payload\Decoders\PayloadDecoderInterface;
	use App\Service\ResponseHelper;
	use App\Transformer\EntityTransformerInterface;
	use App\Transformer\UserEntityTransformer;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	abstract class AbstractBaseApiController extends AbstractController {

		/**
		 * @var ValidatorInterface
		 */
		protected ValidatorInterface $validator;

		/**
		 * @var ServiceEntityRepository
		 */
		protected ServiceEntityRepository $repository;

		/**
		 * @var RequestDTOTransformerInterface
		 */
		protected RequestDTOTransformerInterface $DTOTransformer;

		/**
		 * @var EntityTransformerInterface
		 */
		protected EntityTransformerInterface $entityTransformer;

		/**
		 * @var PayloadDecoderInterface|null
		 */
		private ?PayloadDecoderInterface $payloadDecoder;

		/**
		 * AbstractBaseApiController constructor.
		 *
		 * @param ValidatorInterface $validator
		 * @param EntityTransformerInterface $entityTransformer
		 * @param RequestDTOTransformerInterface $DTOTransformer
		 * @param ServiceEntityRepository $repository
		 * @param PayloadDecoderInterface|null $payloadDecoder
		 */
		public function __construct(
			ValidatorInterface $validator,
			EntityTransformerInterface $entityTransformer,
			RequestDTOTransformerInterface $DTOTransformer,
			ServiceEntityRepository $repository,
			?PayloadDecoderInterface $payloadDecoder = null
		) {
			$this->validator = $validator;
			$this->DTOTransformer = $DTOTransformer;
			$this->entityTransformer = $entityTransformer;
			$this->repository = $repository;
			$this->payloadDecoder = $payloadDecoder ?? null;
		}

		/**
		 * createOne is the legacy method. I've started refactoring how a lot of these methods work.
		 * The new method is doCreate.
		 *
		 * @param Request $request
		 * @param bool $skipValidation
		 * @param bool $getUser
		 *
		 * @return EntityInterface
		 * @throws ValidationException
		 */
		protected function createOne(Request $request, bool $skipValidation = false, bool $getUser = true
		): EntityInterface {

			/**Set user to null if the resource does not require owner. Such as @See UserEntityTransformer
			 */
			$user = null;
			if ($getUser)
				$user = $this->getUser();

			$dto = $this->DTOTransformer->transformFromRequest($request);

			if (!$skipValidation)
				$this->validateDTO($dto);

			return $this->entityTransformer->create($dto, $user);

		}

		protected function doCreate(EntityTransformerInterface $transformer, Request $request): Response {
			try {
				$payload = $this->payloadDecoder->parse(DecoderIntent::CREATE, $request->getContent());
			} catch (PayloadDecoderException | ValidationException $exception) {
				return $this->handleApiException($request, $exception);
			}

			//TODO wrap this in a try-catch block
			$entity = $transformer->create($payload);

			//TODO this is for testing. Check out how https://github.com/LartTyler/php-api-common responds
			return ResponseHelper::createResourceCreatedResponse('games/read/' . $entity->getId());

		}

		/**
		 * @return User
		 */
		protected function getUser(): User {

			$user = parent::getUser();

			if (!($user instanceof User)) throw new \InvalidArgumentException(($user::class . ' not instance of User.'));

			return $user;

		}

		/**
		 * @param DTOInterface $dto
		 * @throws ValidationException
		 */
		protected function validateDTO(DTOInterface $dto): void {

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0)
				throw new ValidationException($errors);

		}

		/**
		 * @param Request $request
		 * @param int $id
		 *
		 * @return EntityInterface
		 */
		protected function updateOne(Request $request, int $id): EntityInterface {

			if (!$this->doesEntityExist($id)) throw new NotFoundHttpException('resource does not exist');

			$this->confirmResourceOwner($this->repository->find($id));

			return $this->entityTransformer->update($id, $request);

		}

		/**
		 * @param int $id
		 *
		 * @return bool
		 */
		private function doesEntityExist(int $id): bool {

			$entity = $this->repository->find($id);

			if (!$entity) {
				return false;
			}

			return true;

		}

		/**
		 * @param Object $entity
		 */
		private function confirmResourceOwner(object $entity): void {

			if (!method_exists($entity, 'getOwner') && !method_exists($entity, 'getLikedBy')) {
				throw new \BadMethodCallException($entity::class . ' does not have getOwner or getLiked methods');
			}

			$authenticatedUser = $this->getUser();
			$owner = $entity->getOwner() ?? $entity->getLikedBy();

			if ($owner !== $authenticatedUser) {
				throw new AccessDeniedHttpException();
			}

		}

		/**
		 * @param int $id
		 */
		protected function deleteOne(int $id): void {

			if (!$this->doesEntityExist($id)) throw new NotFoundHttpException('resource does not exist');

			$this->confirmResourceOwner($this->repository->find($id));

			$this->entityTransformer->delete($id);

		}

		//TODO handle API exceptions better... Look at https://github.com/LartTyler/php-api-common for ideas.
		/**
		 * @param Request $request
		 * @param \Exception $exception
		 * @return Response
		 */
		protected function handleApiException(Request $request, \Exception $exception): Response {

			if ($exception instanceof ValidationException)
				return ResponseHelper::createValidationErrorResponse($exception);

			else if ($exception instanceof PayloadDecoderException)
				return ResponseHelper::createJsonErrorResponse($exception->getMessage(), 'error');

			else
				return ResponseHelper::createJsonErrorResponse('unknown api error', 'error');

		}

		abstract protected function create(Request $request): Response;

		abstract protected function update(Request $request, int $id): Response;

		abstract protected function delete(int $id): Response;

		abstract protected function read(int $id, SerializerInterface $serializer): Response;

	}