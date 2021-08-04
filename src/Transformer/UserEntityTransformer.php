<?php
	namespace App\Transformer;

	use App\DTO\User\UserDTO;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class UserEntityTransformer extends AbstractEntityTransformer {

		private UserPasswordHasherInterface $encoder;

		#[Pure]
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
		                                    UserPasswordHasherInterface $encoder) {
			parent::__construct($entityManager, $validator);
			$this->encoder = $encoder;
		}

		public function doCreateWork(): EntityInterface {
			assert ($this->dto instanceof UserDTO);

			$user = new User ($this->dto->email, $this->dto->username);

			$password = $this->encoder->hashPassword($user, $this->dto->password);

			$user->setPassword($password);

			return $user;

		}

		protected function doUpdateWork(int $id, Request $request, bool $skipValidation): EntityInterface {
			// TODO: Implement doUpdateWork() method.
		}
	}