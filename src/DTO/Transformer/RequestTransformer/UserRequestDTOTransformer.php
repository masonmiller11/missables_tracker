<?php
	namespace App\DTO\Transformer\RequestTransformer;

	use App\DTO\User\UserDTO;
	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\Request;

	class UserRequestDTOTransformer extends AbstractRequestDTOTransformer {

		public function transformFromRequest(Request $request): UserDTO {

			$data = json_decode($request->getContent(), true);

			$dto = new UserDTO();

			if (!isset($data['username'])) {
				throw new \OutOfBoundsException('users must include username');
			}

			if (!isset($data['email'])) {
				throw new \OutOfBoundsException('users must include email');
			}

			if (!isset($data['password'])) {
				throw new \OutOfBoundsException('users must include password');
			}

			$dto->username = $data['username'];
			$dto->email = $data['email'];
			$dto->password = $data['password'];

			return $dto;

		}

	}