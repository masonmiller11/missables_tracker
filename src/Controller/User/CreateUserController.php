<?php
	namespace App\Controller\User;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\UserRequestDTOTransformer;
	use App\DTO\User\UserDTO;
	use App\Entity\User;
	use App\Exception\ValidationException;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use App\Transformer\UserEntityTransformer;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 *
	 * @Route(path="/signup", name="user.")
	 */
	final class CreateUserController extends AbstractBaseApiController {


		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 * @param UserRequestDTOTransformer $DTOtransformer
		 * @param UserEntityTransformer $entityTransformer
		 * @param UserPasswordHasherInterface $encoder
		 * @param EntityManagerInterface $entityManager
		 * @return Response
		 */
		public function create (Request $request, UserRequestDTOTransformer $DTOtransformer,
		                        UserEntityTransformer $entityTransformer, UserPasswordHasherInterface $encoder,
		                        EntityManagerInterface $entityManager): Response {

			$dto = $DTOtransformer->transformFromRequest($request);

			Assert($dto instanceof UserDTO);

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

			$user = new User ($dto->email, $dto->username);

			$password = $encoder->hashPassword($user, $dto->password);

			$user->setPassword($password);

			$entityManager->persist($user);
			$entityManager->flush();

			return $this->responseHelper->createResourceCreatedResponse('users/read' . $user->getId());

		}
	}