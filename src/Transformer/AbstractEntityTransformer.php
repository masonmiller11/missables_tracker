<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\Exception\ValidationException;
	use Doctrine\ORM\EntityManagerInterface;
	use http\Exception\RuntimeException;
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
		protected function validate(DTOInterface $dto): void {

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

		}

		public function delete(int $id): void{

			if (!isset($this->repository)) {
				throw new RuntimeException('repository is not set in ' . static::class);
			}

			$section = $this->repository->find($id);

			$this->entityManager->remove($section);
			$this->entityManager->flush();

			//TODO test and then delete this

		}

	}