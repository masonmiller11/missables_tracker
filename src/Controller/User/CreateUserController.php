<?php
	namespace App\Controller\User;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\UserRequestDTOTransformer;
	use App\DTO\User\UserDTO;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use App\Transformer\UserEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 *
	 * @Route(path="/user/create", name="user.")
	 */
	final class CreateUserController extends AbstractBaseApiController {


		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 * @param UserRequestDTOTransformer $DTOtransformer
		 * @param UserEntityTransformer $entityTransformer
		 * @return Response
		 * @throws \Exception
		 */
		public function create (Request $request, UserRequestDTOTransformer $DTOtransformer,
		                        UserEntityTransformer $entityTransformer): Response {

			$user = $this->createOne($request, $DTOtransformer, UserDTO::class, $entityTransformer);

			return $this->responseHelper->createResourceCreatedResponse('users/read' . $user->getId());

		}
	}