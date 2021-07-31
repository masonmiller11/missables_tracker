<?php
	namespace App\DTO\Step;

	class StepDTO extends AbstractStepDTO {

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $sectionId;

	}