<?php

	namespace App\Controller;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\RequestTransformer\RequestDTOTransformerInterface;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use App\Transformer\EntityTransformerInterface;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Exception\InvalidArgumentException;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;
	use Symfony\Component\HttpFoundation\Request;

	abstract class AbstractBaseApiController extends AbstractController {

		/**
		 * @var RequestStack
		 */
		protected RequestStack $request;

		/**
		 * @var ValidatorInterface
		 */
		protected ValidatorInterface $validator;

		/**
		 * @var ServiceEntityRepository
		 */
		protected ServiceEntityRepository $repository;

		/**
		 * @var EntityManagerInterface
		 */
		protected EntityManagerInterface $entityManager;

		protected RequestDTOTransformerInterface $DTOTransformer;

		protected EntityTransformerInterface $entityTransformer;

		/**
		 * AbstractBaseApiController constructor.
		 *
		 * @param RequestStack                   $request
		 * @param EntityManagerInterface         $entityManager
		 * @param ValidatorInterface             $validator
		 * @param EntityTransformerInterface     $entityTransformer
		 * @param RequestDTOTransformerInterface $DTOTransformer
		 * @param ServiceEntityRepository        $repository
		 */
		public function __construct (RequestStack $request,
									 EntityManagerInterface $entityManager, ValidatorInterface $validator,
									 EntityTransformerInterface $entityTransformer,
									 RequestDTOTransformerInterface $DTOTransformer, ServiceEntityRepository $repository
								) {

			$this->request = $request;
			$this->validator = $validator;
			$this->entityManager = $entityManager;
			$this->DTOTransformer = $DTOTransformer;
			$this->entityTransformer = $entityTransformer;
			$this->repository = $repository;

		}

		/**
		 * @return User
		 */
		protected function getUser(): User {
			$user = parent::getUser();
			assert($user instanceof User);

			return $user;
		}

		/**
		 * @param DTOInterface $dto
		 * @throws ValidationFailedException
		 */
		protected function validateDTO(DTOInterface $dto): void {

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) throw new ValidationFailedException($errors->count(), $errors);

		}

		/**
		 * @param Object $entity
		 */
		private function confirmResourceOwner (Object $entity): void {

			if (!(method_exists($entity, 'getOwner') && method_exists($entity, 'getLikedBy'))) {
				throw new InvalidArgumentException();
			}

			$authenticatedUser = $this->getUser();
			$owner =  $entity->getOwner() ?? $entity->getLikedBy();

			if ($owner !== $authenticatedUser) {
				throw new AccessDeniedHttpException;
			}

		}

		/**
		 * @param int $id
		 *
		 * @return void
		 */
		private function doesEntityExist(int $id): void {

			$entity = $this->repository->find($id);

			if (!$entity) {
				throw new NotFoundHttpException('resource does not exist');
			}

		}

		/**
		 * @param Request $request
		 *
		 * @return EntityInterface
		 */
		protected function createOne (Request $request, $skipValidation = false): EntityInterface {

			$user = $this->getUser();

			$dto = $this->DTOTransformer->transformFromRequest($request);

			if (!$skipValidation) $this->validateDTO($dto);

			return $this->entityTransformer->create($dto, $user);

		}

		/**
		 * @param Request $request
		 * @param int     $id
		 *
		 * @return EntityInterface
		 */
		protected function updateOne (Request $request, int $id): EntityInterface {

			$this->doesEntityExist($id);
			$this->confirmResourceOwner($this->repository->find($id));

			return $this->entityTransformer->update($id, $request);

		}

		/**
		 * @param int $id
		 */
		protected function deleteOne (int $id): void {

			$this->doesEntityExist($id);
			$this->confirmResourceOwner($this->repository->find($id));

			$this->entityTransformer->delete($id);

		}

		abstract protected function create(Request $request): Response;

		abstract protected function update(Request $request, int $id): Response;

		abstract protected function delete(int $id): Response;

		abstract protected function read(int $id): Response;

	}