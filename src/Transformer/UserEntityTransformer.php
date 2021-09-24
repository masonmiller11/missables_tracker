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

		/**
		 * @throws ValidationException
		 */
		public function updatePassword(int $id, string $password, bool $skipValidation = false): User {

			$user = $this->repository->find($id);

			if (!($user instanceof User)) {
				throw new \InvalidArgumentException($user::class . ' not instance of User');
			}

			$this->dto = new UserDTO();
			$this->dto->username = $user->getUsername();
			$this->dto->email = $user->getEmail();
			$this->dto->password = $password;

			if (!$skipValidation) $this->validate($this->dto);

			$password = $this->encoder->hashPassword($user, $this->dto->password);

			$user->setPassword($password);

			$this->entityManager->persist($user);
			$this->entityManager->flush();

			return $user;

		}

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
		 * @throws ValidationException
		 */
		protected function doUpdateWork(int $id, Request $request, bool $skipValidation): User {

			$user = $this->repository->find($id);

			if (!($user instanceof User)) {
				throw new \InvalidArgumentException($user::class . ' not instance of User. Does ' . $id . 'belong to a user?');
			}

			$data = json_decode($request->getContent(), true);

			$tempDTO = new UserDTO();
			$tempDTO->password = $user->getPassword();

			if (!isset($data['username']) && !isset($data['email'])) {
				throw new \OutOfBoundsException('request must include include username or email');
			}

			/**If username is not present in the request data, then create a temporary username called 'fake username'
			 * This is simply so that the $tempDTO will pass validation even if $data does not include username
			 * After we do this for username, we do it for email as well.
			 */
			if (!isset($data['username'])) {
				$tempDTO->username = 'fake username';
			} else {
				$tempDTO->username = $data['username'];
				$user->setUsername($tempDTO->username);
			}

			if (!isset($data['email'])) {
				$tempDTO->email = 'fake@example.com';
			} else {
				$tempDTO->email = $data['email'];
				$user->setEmail($tempDTO->email);
			}

			if (!$skipValidation) $this->validate($tempDTO);

			return $user;

		}
	}