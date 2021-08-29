<?php

	namespace App\Controller;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\RequestTransformer\RequestDTOTransformerInterface;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Transformer\EntityTransformerInterface;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Exception\InvalidArgumentException;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
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
		 * AbstractBaseApiController constructor.
		 *
		 * @param ValidatorInterface $validator
		 * @param EntityTransformerInterface $entityTransformer
		 * @param RequestDTOTransformerInterface $DTOTransformer
		 * @param ServiceEntityRepository $repository
		 */
		public function __construct(
			ValidatorInterface $validator, EntityTransformerInterface $entityTransformer,
			RequestDTOTransformerInterface $DTOTransformer, ServiceEntityRepository $repository
		) {

			$this->validator = $validator;
			$this->DTOTransformer = $DTOTransformer;
			$this->entityTransformer = $entityTransformer;
			$this->repository = $repository;

		}

		/**
		 * @param Request $request
		 * @param bool $skipValidation
		 * @param bool $getUser
		 *
		 * @return EntityInterface
		 * @throws ValidationFailedException
		 */
		protected function createOne(Request $request, bool $skipValidation = false, bool $getUser = true
		): EntityInterface {

			$user = null;

			if ($getUser)
				$user = $this->getUser();

			$dto = $this->DTOTransformer->transformFromRequest($request);

			if (!$skipValidation)
				$this->validateDTO($dto);

			return $this->entityTransformer->create($dto, $user);

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
		 *
		 * @throws ValidationFailedException
		 */
		protected function validateDTO(DTOInterface $dto): void {

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0)
				throw new ValidationFailedException($errors->count(), $errors);

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
				throw new InvalidArgumentException();
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

		abstract protected function create(Request $request): Response;

		abstract protected function update(Request $request, int $id): Response;

		abstract protected function delete(int $id): Response;

		abstract protected function read(int $id, SerializerInterface $serializer): Response;

	}