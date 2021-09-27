<?php

	namespace App\Controller;

	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\DecoderIntent;
	use App\Payload\Decoders\PayloadDecoderInterface;
	use App\Payload\Decoders\SymfonyDeserializeDecoder;
	use App\Service\ResponseHelper;
	use App\Transformer\EntityTransformerInterface;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	abstract class AbstractBaseApiController extends AbstractController {


		/**
		 * @var ServiceEntityRepository
		 */
		protected ServiceEntityRepository $repository;

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
		 * @param EntityTransformerInterface $entityTransformer
		 * @param ServiceEntityRepository $repository
		 * @param PayloadDecoderInterface|null $payloadDecoder
		 * @see SymfonyDeserializeDecoder
		 *
		 */
		public function __construct(
			EntityTransformerInterface $entityTransformer,
			ServiceEntityRepository $repository,
			?PayloadDecoderInterface $payloadDecoder = null
		) {
			$this->entityTransformer = $entityTransformer;
			$this->repository = $repository;
			$this->payloadDecoder = $payloadDecoder ?? null;
		}

		/**
		 * The doCreate method is meant to replace createOne.
		 * Eventually there will be a doUpdate as well which will replace updateOne
		 *
		 * @param Request $request
		 * @param User|null $user
		 * @return EntityInterface
		 * @throws ValidationException
		 */
		protected function doCreate(Request $request, User $user = null): EntityInterface {

			$payload = $this->payloadDecoder->parse(DecoderIntent::CREATE, $request->getContent());

			return $this->entityTransformer->create($payload, $user);

		}

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

		/**
		 * @param Request $request
		 * @param int $id
		 * @param User|null $user
		 * @return EntityInterface
		 * @throws ValidationException
		 */
		protected function doUpdate(Request $request, int $id, User $user = null): EntityInterface {

			if (!$this->doesEntityExist($id))
				throw new NotFoundHttpException('resource does not exist');

			$this->confirmResourceOwner($this->repository->find($id));

			$payload = $this->payloadDecoder->parse(DecoderIntent::UPDATE, $request->getContent());

			return $this->entityTransformer->update($payload, $id);
		}

//		/**
//		 * @param Request $request
//		 * @param int $id
//		 *
//		 * @return EntityInterface
//		 */
//		protected function updateOne(Request $request, int $id): EntityInterface {
//
//			if (!$this->doesEntityExist($id))
//				throw new NotFoundHttpException('resource does not exist');
//
//			$this->confirmResourceOwner($this->repository->find($id));
//
//			return $this->entityTransformer->update($id, $request);
//
//		}

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
		 * @return User
		 */
		protected function getUser(): User {

			$user = parent::getUser();

			if (!($user instanceof User)) throw new \InvalidArgumentException(($user::class . ' not instance of User.'));

			return $user;

		}

		//TODO handle API exceptions better... Look at https://github.com/LartTyler/php-api-common for ideas.

		/**
		 * @param int $id
		 */
		protected function deleteOne(int $id): void {

			if (!$this->doesEntityExist($id)) throw new NotFoundHttpException('resource does not exist');

			$this->confirmResourceOwner($this->repository->find($id));

			$this->entityTransformer->delete($id);

		}

	}