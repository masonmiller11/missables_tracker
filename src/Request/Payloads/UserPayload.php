<?php
	namespace App\Request\Payloads;

	use App\Validator as CustomAssert;
	use Symfony\Component\Validator\Constraints as Assert;

	class UserPayload implements PayloadInterface {

		use PayloadTrait;

		/**
		 * @CustomAssert\Password()
		 */
		public mixed $password;

		/**
		 * @Assert\NotBlank(groups={"create"})
		 * @Assert\Email(
		 *     message = "The email '{{ value }}' is not a valid email."
		 * )
		 */
		public mixed $email;

		/**
		 *
		 * @Assert\Length(
		 *      min = 2,
		 *      max = 50,
		 *      minMessage = "Your username must be at least {{ limit }} characters long",
		 *      maxMessage = "Your usernamename cannot be longer than {{ limit }} characters"
		 * )
		 */
		public mixed $username;

	}