<?php
	namespace App\DTO\Step;

	use Symfony\Component\Validator\Constraints as Assert;

	class StepDTO extends AbstractStepDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $sectionId;

	}