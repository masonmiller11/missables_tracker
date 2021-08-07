<?php
	namespace App\DTO\Step;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	abstract class AbstractStepDTO implements DTOInterface {

		/**
		 * @Assert\NotBlank ()
		 */
		public mixed $description;

		/**
		 * @Assert\NotBlank ()
		 */
		public mixed $name;

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $position;

	}