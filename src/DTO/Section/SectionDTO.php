<?php

	namespace App\DTO\Section;

	class SectionDTO extends  AbstractSectionDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $playthroughId;

	}