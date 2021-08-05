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

	/***
	 * @Route(path="/user/update", name="user.update")
	 */
	final class UpdateUserController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"PATCH"})
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
		 * @Route(path="/password", methods={"PATCH"})
		 *
		 * @param Request               $request
		 * @param UserEntityTransformer $userEntityTransformer
		 *
		 * @return Response
		 */
		public function updatePassword(Request $request, UserEntityTransformer $userEntityTransformer): Response {

			$data = json_decode($request->getContent());

			if (!isset($data['password'])) {
				throw new ValidationException('new users must include password');
			}

			$password = $data['password'];

			$userId = $this->getUser()->getId();

			$userEntityTransformer->updatePassword($userId, $password);

			return ResponseHelper::createUserUpdatedResponse();
		}

	}