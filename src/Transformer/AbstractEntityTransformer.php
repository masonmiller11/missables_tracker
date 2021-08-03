<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\RequestTransformer\AbstractRequestDTOTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Exception\ValidationException;
	use App\Repository\AbstractBaseRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use http\Exception\RuntimeException;
	use Symfony\Component\HttpFoundation\Request;
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

		/**
		 * @var AbstractBaseRepository
		 */
		protected AbstractBaseRepository $repository;

		/**
		 * @var AbstractRequestDTOTransformer
		 */
		protected AbstractRequestDTOTransformer $DTOTransformer;

		/**
		 * @var User
		 */
		protected User $user;

		/**
		 * @var DTOInterface
		 */
		protected DTOInterface $dto;

		/**
		 * AbstractEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 */
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)  {

			$this->entityManager = $entityManager;
			$this->validator = $validator;

		}

		abstract protected function doCreateWork(): EntityInterface;

		abstract protected function doUpdateWork(int $id, Request $request, bool $skipValidation): EntityInterface;

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

			$entity = $this->repository->find($id);

			$this->entityManager->remove($entity);
			$this->entityManager->flush();

		}

		public function create(DTOInterface $dto, User $user, bool $skipValidation = false): EntityInterface {

			$this->dto = $dto;
			$this->user = $user;

			if (!$skipValidation) {
				$this->validate($dto);
			}

			$entity = $this->doCreateWork();

			$this->entityManager->persist($entity);
			$this->entityManager->flush();

			return $entity;

		}

		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			if (!method_exists($this, 'doUpdateWork')) {
				throw new RuntimeException('cannot run update on ' . static::class);
			}

			$entity = $this->doUpdateWork($id, $request, $skipValidation);

			$this->entityManager->persist($entity);
			$this->entityManager->flush();

			return $entity;

		}

	}