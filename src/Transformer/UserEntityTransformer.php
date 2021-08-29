<?php
	namespace App\Transformer;

	use App\DTO\Transformer\RequestTransformer\UserRequestDTOTransformer;
	use App\DTO\User\UserDTO;
	use App\Entity\User;
	use App\Exception\ValidationException;
	use App\Repository\UserRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class UserEntityTransformer extends AbstractEntityTransformer {

		private UserPasswordHasherInterface $encoder;

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
		 * @return User
		 */
		public function doCreateWork(): User {

			$user = $this->repository->find($id);

			$user = new User ($this->dto->email, $this->dto->username);

			$password = $this->encoder->hashPassword($user, $this->dto->password);

			$user->setPassword($password);

			return $user;

		}

		public function updatePassword(int $id, string $password, bool $skipValidation = false): User {

			if (!($this->dto instanceof UserDTO)) {
				throw new \InvalidArgumentException('UserEntityTransformer\'s DTO not instance of UserDTO');

			$this->dto = new UserDTO();
			$this->dto->username = $user->getUsername();
			$this->dto->email = $user->getEmail();
			$this->dto->password = $password;

			$this->validate($this->dto);

			$password = $this->encoder->hashPassword($user, $this->dto->password);

			$user->setPassword($password);

			$this->entityManager->persist($user);
			$this->entityManager->flush();

			return $user;

		}

		protected function doUpdateWork(int $id, Request $request, bool $skipValidation): User {

			$user = $this->repository->find($id);
			Assert($user instanceof User);

			$data = json_decode($request->getContent(), true);

			$tempDTO = new UserDTO();
			$tempDTO->password = $user->getPassword();

			if (!isset($data['username']) && !isset($data['email'])) {
				throw new ValidationException('request does not include username or email');
			}

			//if it's not in the request, we'll set some temp data so it passed validation.
			//TODO this shit is sort of jank. We need to rethink DTO validation to fix it...
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