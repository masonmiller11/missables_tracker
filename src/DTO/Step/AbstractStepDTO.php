<?php
	namespace App\DTO\Step;

	use App\DTO\DTOInterface;

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