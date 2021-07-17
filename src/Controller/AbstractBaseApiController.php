<?php

	namespace App\Controller;

	use App\DTO\DTOInterface;
	use App\DTO\Transformer\RequestTransformer\RequestDTOTransformerInterface;
	use App\DTO\Transformer\ResponseTransformer\ResponseDTOTransformerInterface;
	use App\Entity\User;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
	use App\Service\EntityHelper;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

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
		 * @var EntityHelper
		 */
		protected EntityHelper $entityHelper;

		/**
		 * @var ValidatorInterface
		 */
		protected ValidatorInterface $validator;

		/**
		 * @var GameRepository
		 */
		protected GameRepository $gameRepository;

		/**
		 * @var ResponseDTOTransformerInterface
		 */
		protected ResponseDTOTransformerInterface $responseDTOTransformer;

		/**
		 * @var RequestDTOTransformerInterface
		 */
		protected RequestDTOTransformerInterface $requestDTOTransformer;

		/**
		 * @var ServiceEntityRepository
		 */
		protected ServiceEntityRepository $repository;

		/**
		 * AbstractBaseApiController constructor.
		 *
		 * @param IGDBHelper         $IGDBHelper
		 * @param ResponseHelper     $responseHelper
		 * @param EntityHelper       $entityHelper
		 * @param RequestStack       $request
		 * @param ValidatorInterface $validator
		 * @param GameRepository     $gameRepository
		 */
		public function __construct (IGDBHelper $IGDBHelper,
									 ResponseHelper $responseHelper,
									 EntityHelper $entityHelper,
									 RequestStack $request,
									 ValidatorInterface $validator,
									 GameRepository $gameRepository) {

			$this->IGDBHelper = $IGDBHelper;
			$this->responseHelper = $responseHelper;
			$this->request = $request;
			$this->entityHelper = $entityHelper;
			$this->validator = $validator;
			$this->gameRepository = $gameRepository;

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
		 * @throws \Exception
		 */
		protected function validateOne(DTOInterface $dto) {

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

		}

		/**
		 * @param iterable $dtos
		 * @throws \Exception
		 */
		protected function validateMany(iterable $dtos) {

			$errors = $this->validator->validate($dtos);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

		}

		/**
		 * @param ResponseDTOTransformerInterface $transformer
		 */
		protected function setResponseDTOTransformer(ResponseDTOTransformerInterface $transformer): void {
			$this->responseDTOTransformer = $transformer;
		}

		/**
		 * @param RequestDTOTransformerInterface $transformer
		 */
		protected function setRequestDTOTransformer(RequestDTOTransformerInterface $transformer): void {
			$this->requestDTOTransformer = $transformer;
		}
		/**
		 * @param Object $object
		 *
		 * @return DTOInterface
		 */
		protected abstract function transformOne(Object $object): DTOInterface;

		/**
		 * @param iterable $objects
		 *
		 * @return iterable
		 */
		protected abstract function transformMany(iterable $objects): iterable;

	}