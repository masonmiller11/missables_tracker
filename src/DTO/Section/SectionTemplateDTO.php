<?php

	namespace App\DTO\Section;

	use Symfony\Component\Validator\Constraints as Assert;

	class SectionTemplateDTO extends  AbstractSectionDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $templateId;

	}