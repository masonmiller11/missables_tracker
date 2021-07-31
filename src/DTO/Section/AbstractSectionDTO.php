<?php
	namespace App\DTO\Section;

	abstract class AbstractSectionDTO {

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