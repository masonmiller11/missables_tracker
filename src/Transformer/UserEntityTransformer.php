<?php
	namespace App\Transformer;

	use App\DTO\Transformer\RequestTransformer\UserRequestDTOTransformer;
	use App\DTO\User\UserDTO;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class UserEntityTransformer extends AbstractEntityTransformer {

		private UserPasswordHasherInterface $encoder;

		#[Pure]
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
		                                    UserPasswordHasherInterface $encoder, UserRequestDTOTransformer $DTOTransformer) {
			parent::__construct($entityManager, $validator);
			$this->encoder = $encoder;
			$this->DTOTransformer = $DTOTransformer;
		}

		public function doCreateWork(): EntityInterface {
			assert ($this->dto instanceof UserDTO);

			$user = new User ($this->dto->email, $this->dto->username);

			$password = $this->encoder->hashPassword($user, $this->dto->password);

			$user->setPassword($password);

			return $user;

		}

		protected function doUpdateWork(int $id, Request $request, bool $skipValidation): User {

			$user = $this->repository->find($id);

			Assert($user instanceof User);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$this->validate($tempDTO);

			$user->setEmail($tempDTO->email);
			$user->setUsername($tempDTO->username);

			return $user;

		}

		public function updatePassword(int $id, string $password, bool $skipValidation = false): User {

			$user = $this->repository->find($id);
			Assert($user instanceof User);

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
	}