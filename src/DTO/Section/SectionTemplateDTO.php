<?php

	namespace App\DTO\Section;

	class SectionTemplateDTO extends  AbstractSectionDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $templateId;

	}