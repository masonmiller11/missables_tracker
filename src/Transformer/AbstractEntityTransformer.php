<?php
	namespace App\Transformer;

	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Repository\AbstractBaseRepository;
	use App\Request\Payloads\PayloadInterface;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Validator\Exception\InvalidArgumentException;

	abstract class AbstractEntityTransformer implements EntityTransformerInterface {

		/**
		 * @var EntityManagerInterface
		 */
		protected EntityManagerInterface $entityManager;

		/**
		 * @var AbstractBaseRepository
		 */
		protected AbstractBaseRepository $repository;

		/**
		 * @var User|null
		 */
		protected ?User $user;

		/**
		 * @var Object|null
		 */
		protected ?object $dto;

		/**
		 * @var int|null
		 */
		protected ?int $id;

		/**
		 * AbstractEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param AbstractBaseRepository $repository
		 */
		public function __construct(EntityManagerInterface $entityManager, AbstractBaseRepository $repository) {

			$this->entityManager = $entityManager;
			$this->repository = $repository;

		}

		/**
		 * @param int $id
		 */
		public function delete(int $id): void {

			if (!isset($this->repository))
				throw new InvalidArgumentException('repository is not set in ' . static::class);

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

	}