<?php
	namespace App\Transformer;

	use App\Entity\User;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidEntityException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\UserRepository;
	use App\Request\Payloads\UserPayload;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

	final class UserEntityTransformer extends AbstractEntityTransformer {

		private UserPasswordHasherInterface $encoder;

		/**
		 * UserEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param UserPasswordHasherInterface $encoder
		 * @param UserRepository $repository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            UserPasswordHasherInterface $encoder,
		                            UserRepository $repository) {

			parent::__construct($entityManager, $repository);

			$this->encoder = $encoder;

		}

		/**
		 * @return User
		 */
		protected function doCreateWork(): User {

			if (!($this->dto instanceof UserPayload))
				throw new InvalidPayloadException(UserPayload::class, $this->dto::class);

			$user = new User ($this->dto->email, $this->dto->username);

			$password = $this->encoder->hashPassword($user, $this->dto->password);

			$user->setPassword($password);

			return $user;

		}

		/**
		 * @return User
		 */
		protected function doUpdateWork(): User {

			if (!($this->repository instanceof UserRepository))
				throw new InvalidRepositoryException(UserRepository::class, $this->repository::class);

			$user = $this->checkAndSetData($this->repository->find($this->id));

			if (!($user instanceof User))
				throw new InvalidEntityException(User::class, $user::class);

			return $user;

		}

		private function checkAndSetData(User $user): User {

			if (!($this->dto instanceof UserPayload))
				throw new InvalidPayloadException(UserPayload::class, $this->dto::class);

			$this->dto->email ?? $user->setEmail($this->dto->email);

			$this->dto->username ?? $user->setUsername($this->dto->email);

			$this->dto->password ?? $user->setPassword($this->encoder->hashPassword($user, $this->dto->password));

			return $user;

		}
	}