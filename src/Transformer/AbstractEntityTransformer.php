<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\Exception\ValidationException;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	Abstract class AbstractEntityTransformer implements EntityTransformerInterface {

		/**
		 * @var EntityManagerInterface
		 */
		protected EntityManagerInterface $entityManager;

		/**
		 * @var ValidatorInterface
		 */
		protected ValidatorInterface $validator;

		public function __construct(EntityManagerInterface $entityManager,
									ValidatorInterface $validator)  {

			$this->entityManager = $entityManager;
			$this->validator = $validator;

		}

		/**
		 * @param DTOInterface $dto
		 * @throws ValidationException
		 */
		protected function validate(DTOInterface $dto) {

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

		}

	}