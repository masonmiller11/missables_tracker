<?php
	namespace App\Transformer;

	use App\DTO\Transformer\RequestTransformer\UserRequestDTOTransformer;
	use App\DTO\User\UserDTO;
	use App\Entity\User;
	use App\Exception\ValidationException;
	use App\Repository\UserRepository;
	use App\Request\Payloads\UserPayload;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class UserEntityTransformer extends AbstractEntityTransformer {

		private UserPasswordHasherInterface $encoder;

		/**
		 * UserEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param UserPasswordHasherInterface $encoder
		 * @param UserRequestDTOTransformer $DTOTransformer
		 * @param UserRepository $repository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
		                            UserPasswordHasherInterface $encoder, UserRequestDTOTransformer $DTOTransformer,
		                            UserRepository $repository) {
			parent::__construct($entityManager, $validator);
			$this->encoder = $encoder;
			$this->DTOTransformer = $DTOTransformer;
			$this->repository = $repository;
		}

		//TODO delete this
//		/**
//		 * @throws ValidationException
//		 */
//		public function updatePassword(int $id, string $password, bool $skipValidation = false): User {
//
//			$user = $this->repository->find($id);
//
//			if (!($user instanceof User)) {
//				throw new \InvalidArgumentException($user::class . ' not instance of User');
//			}
//
//			$this->dto = new UserDTO();
//			$this->dto->username = $user->getUsername();
//			$this->dto->email = $user->getEmail();
//			$this->dto->password = $password;
//
//			if (!$skipValidation) $this->validate($this->dto);
//
//			$password = $this->encoder->hashPassword($user, $this->dto->password);
//
//			$user->setPassword($password);
//
//			$this->entityManager->persist($user);
//			$this->entityManager->flush();
//
//			return $user;
//
//		}

		/**
		 * @return User
		 */
		protected function doCreateWork(): User {

			if (!($this->dto instanceof UserPayload)) {
				throw new \InvalidArgumentException('UserEntityTransformer\'s DTO not instance of UserDTO');
			}

			$user = new User ($this->dto->email, $this->dto->username);

			$password = $this->encoder->hashPassword($user, $this->dto->password);

			$user->setPassword($password);

			return $user;

		}

		/**
		 * @return User
		 */
		protected function doUpdateWork(): User {

			$user = $this->checkAndSetData($this->repository->find($this->id));

			if (!($user instanceof User))
				throw new \InvalidArgumentException(
					$user::class . ' not instance of User. Does ' . $this->id . 'belong to a user?'
				);

			return $user;

		}

		private function checkAndSetData(User $user): User {

			if(!($this->dto instanceof UserPayload))
				throw new \InvalidArgumentException(
					'In ' . self::class . '. Payload not instance of UserPayload.'
				);

			$this->dto->email ?? $user->setEmail($this->dto->email);

			$this->dto->username ?? $user->setUsername($this->dto->email);

			$this->dto->password ?? $user->setPassword($this->encoder->hashPassword($user, $this->dto->password));

			return $user;

		}
	}