<?php
	namespace App\DTO\Step;

	use Symfony\Component\Validator\Constraints as Assert;

	class StepTemplateDTO extends AbstractStepDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $sectionTemplateId;

	}