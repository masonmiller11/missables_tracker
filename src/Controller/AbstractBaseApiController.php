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
		 * @var EntityHelper
		 */
		protected EntityHelper $entityHelper;

		/**
		 * @var ValidatorInterface
		 */
		protected ValidatorInterface $validator;

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
		 */
		public function __construct (IGDBHelper $IGDBHelper,
									 ResponseHelper $responseHelper,
									 EntityHelper $entityHelper,
									 RequestStack $request,
									 ValidatorInterface $validator) {

			$this->IGDBHelper = $IGDBHelper;
			$this->responseHelper = $responseHelper;
			$this->request = $request;
			$this->entityHelper = $entityHelper;
			$this->validator = $validator;

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
		 * @param Request                        $request
		 * @param RequestDTOTransformerInterface $transformer
		 *
		 * @return DTOInterface
		 * @throws \Exception
		 */
		protected function transformOne(Request $request, RequestDTOTransformerInterface $transformer): DTOInterface {

			$dto = $transformer->transformFromRequest($request);

			$this->validate($dto);

			return $dto;

		}

	}