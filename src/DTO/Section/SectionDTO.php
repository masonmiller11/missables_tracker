<?php

	namespace App\DTO\Section;

	use Symfony\Component\Validator\Constraints as Assert;

	class SectionDTO extends  AbstractSectionDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $playthroughId;

	}