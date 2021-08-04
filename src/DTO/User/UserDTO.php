<?php
	namespace App\DTO\User;

	use App\DTO\DTOInterface;
	use App\Validator as CustomAssert;
	use Symfony\Component\Validator\Constraints as Assert;

	class UserDTO implements DTOInterface {

		/**
		 * @Assert\NotBlank
		 * @CustomAssert\Password
		 */
		public mixed $password;

		/**
		 * /**
		 * @Assert\Email(
		 *     message = "The email '{{ value }}' is not a valid email."
		 * )
		 */
		public mixed $email;

		/**
		 * @Assert\Length(
		 *      min = 2,
		 *      max = 50,
		 *      minMessage = "Your username must be at least {{ limit }} characters long",
		 *      maxMessage = "Your usernamename cannot be longer than {{ limit }} characters"
		 * )
		 */
		public mixed $username;


	}