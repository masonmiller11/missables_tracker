<?php
	namespace App\Controller\User;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Transformer\RequestTransformer\UserRequestDTOTransformer;
	use App\Exception\ValidationException;
	use App\Repository\StepTemplateRepository;
	use App\Repository\UserRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\StepTemplateEntityTransformer;
	use App\Transformer\UserEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\ResponseHeaderBag;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @Route(path="/user/update", name="user.")
	 */
	final class UpdateUserController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"PATCH"}, name="update")
		 *
		 * @param Request               $request
		 * @param UserEntityTransformer $userEntityTransformer
		 *
		 * @return Response
		 */
		public function update(Request $request, UserEntityTransformer $userEntityTransformer): Response {

			$userId = $this->getUser()->getId();

			$userEntityTransformer->update($userId, $request);

			return ResponseHelper::createUserUpdatedResponse();
		}

		/**
		 * @Route(path="/password", methods={"PATCH"}, name="update_password")
		 *
		 * @param Request               $request
		 * @param UserEntityTransformer $userEntityTransformer
		 *
		 * @return Response
		 */
		public function updatePassword(Request $request, UserEntityTransformer $userEntityTransformer): Response {

			$data = json_decode($request->getContent(),true);

			if (!isset($data['password'])) {
				throw new ValidationException('new users must include password');
			}

			$password = $data['password'];

			$userId = $this->getUser()->getId();

			$userEntityTransformer->updatePassword($userId, $password);

			return ResponseHelper::createUserUpdatedResponse();
		}

	}