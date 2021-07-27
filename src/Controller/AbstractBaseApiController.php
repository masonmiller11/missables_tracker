<?php

	namespace App\Controller;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\RequestTransformer\RequestDTOTransformerInterface;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Exception\ValidationException;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use App\Transformer\EntityTransformerInterface;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use http\Exception\InvalidArgumentException;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;
	use Symfony\Component\HttpFoundation\Request;

	abstract class AbstractBaseApiController extends AbstractController {

		/**
		 * @var IGDBHelper
		 */
		protected IGDBHelper $IGDBHelper;

		/**
		 * @var ResponseHelper
		 */
		protected ResponseHelper $responseHelper;

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

		/**
		 * AbstractBaseApiController constructor.
		 *
		 * @param IGDBHelper $IGDBHelper
		 * @param ResponseHelper $responseHelper
		 * @param RequestStack $request
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 */
		public function __construct (IGDBHelper $IGDBHelper,
		                             ResponseHelper $responseHelper,
		                             RequestStack $request,
		                             EntityManagerInterface $entityManager,
		                             ValidatorInterface $validator) {

			$this->IGDBHelper = $IGDBHelper;
			$this->responseHelper = $responseHelper;
			$this->request = $request;
			$this->validator = $validator;
			$this->entityManager = $entityManager;

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
		 * @throws ValidationException
		 */
		protected function validate(DTOInterface $dto) {

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

		}

		/**
		 * @param EntityInterface $entity
		 */
		protected function confirmResourceOwner (EntityInterface $entity) {

			if (!(method_exists($entity, 'getOwner'))) {
				throw new InvalidArgumentException();
			}

			$authenticatedUser = $this->getUser();
			$owner =  $entity->getOwner();

			if ($owner !== $authenticatedUser) {
				throw new AccessDeniedHttpException;
			}

		}

		/**
		 * @param Request                        $request
		 * @param RequestDTOTransformerInterface $transformer
		 *
		 * @return DTOInterface
		 * @throws \Exception
		 */
		protected function transformOne(Request $request, RequestDTOTransformerInterface $transformer): DTOInterface {

			return $transformer->transformFromRequest($request);

		}

		/**
		 * @param Request $request
		 * @param RequestDTOTransformerInterface $dtoTransformer
		 * @param string $type
		 * @param EntityTransformerInterface $entityTransformer
		 *
		 * @return EntityInterface
		 * @throws \Exception
		 */
		protected function doCreate (Request $request,
		                             RequestDTOTransformerInterface $dtoTransformer,
		                             string $type,
		                             EntityTransformerInterface $entityTransformer): EntityInterface {

			$user = $this->getUser();

			$dto = $this->transformOne($request, $dtoTransformer);

			Assert($dto instanceof $type);

			$entity = $entityTransformer->assemble($dto, $user);
			$this->entityManager->persist($entity);
			$this->entityManager->flush();

			return $entity;

		}

		/**
		 * @param Request $request
		 * @param int $id
		 * @param EntityTransformerInterface $entityTransformer
		 * @return EntityInterface
		 */
		protected function doUpdate (Request $request, int $id, EntityTransformerInterface $entityTransformer): EntityInterface {

			return $entityTransformer->update($id, $request);

		}

		/**
		 * @param int $id
		 * @param EntityTransformerInterface $entityTransformer
		 */
		protected function doDelete (int $id, EntityTransformerInterface $entityTransformer): void {

			$entityTransformer->delete($id);

		}

	}