<?php
	namespace App\DTO\Step;

	class StepTemplateDTO extends AbstractStepDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $sectionTemplateId;

	}