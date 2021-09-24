<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\RequestTransformer\AbstractRequestDTOTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Exception\ValidationException;
	use App\Repository\AbstractBaseRepository;
	use App\Request\Payloads\PayloadInterface;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Validator\Exception\InvalidArgumentException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	abstract class AbstractEntityTransformer implements EntityTransformerInterface {

		/**
		 * @var EntityManagerInterface
		 */
		protected EntityManagerInterface $entityManager;

		/**
		 * @var ValidatorInterface
		 */
		protected ValidatorInterface $validator;

		/**
		 * @var AbstractBaseRepository
		 */
		protected AbstractBaseRepository $repository;

		/**
		 * @var AbstractRequestDTOTransformer
		 */
		protected AbstractRequestDTOTransformer $DTOTransformer;

		/**
		 * @var User|null
		 */
		protected ?User $user;

		/**
		 * @var Object|null
		 */
		protected ?Object $dto;

		/**
		 * @var int|null
		 */
		protected ?int $id;

		/**
		 * AbstractEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 */
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator) {

			$this->entityManager = $entityManager;
			$this->validator = $validator;

		}

		/**
		 * @param int $id
		 */
		public function delete(int $id): void {

			if (!isset($this->repository)) {
				throw new InvalidArgumentException('repository is not set in ' . static::class);
			}

			$entity = $this->repository->find($id);

			$this->entityManager->remove($entity);
			$this->entityManager->flush();

		}

		/**
		 * @param PayloadInterface $payload
		 * @param User|null $user
		 * @return EntityInterface
		 */
		public function create(PayloadInterface $payload, User|null $user = null): EntityInterface {

			$this->dto = $payload;
			$this->user = $user;

			$entity = $this->doCreateWork();

			$this->entityManager->persist($entity);
			$this->entityManager->flush();

			return $entity;

		}

		/**
		 * @return EntityInterface
		 */
		abstract protected function doCreateWork(): EntityInterface;

		/**
		 * @param PayloadInterface $payload
		 * @param int $id
		 * @return EntityInterface
		 */
		public function update(PayloadInterface $payload, int $id): EntityInterface {

			$this->dto = $payload;
			$this->id = $id;

			$entity = $this->doUpdateWork();

			$this->entityManager->persist($entity);
			$this->entityManager->flush();

			return $entity;

		}

		/**
		 * @return EntityInterface
		 */
		abstract protected function doUpdateWork(): EntityInterface;

		/**
		 * @param DTOInterface $dto
		 * @throws ValidationException
		 */
		protected function validate(DTOInterface $dto): void {

			$errors = $this->validator->validate($dto);

			if (count($errors) > 0) throw new ValidationException($errors);

		}

	}