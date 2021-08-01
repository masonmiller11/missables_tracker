<?php
	namespace App\DTO\Section;

	use App\DTO\DTOInterface;

	abstract class AbstractSectionDTO implements DTOInterface {

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