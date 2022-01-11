<?php
	namespace App\Request\Payloads;

	use Symfony\Component\Validator\Constraints as Assert;

	class StepPayload implements PayloadInterface {

		use PayloadTrait;

		/**
		 * @Assert\NotBlank(groups={"create"})
		 * @Assert\Type("string")
		 */
		public mixed $description;

		/**
		 * @Assert\NotBlank(groups={"create"})
		 * @Assert\Type("string")
		 */
		public mixed $name;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\Type("integer")
		 */
		public mixed $position;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\Type("integer")
		 */
		public mixed $sectionId;

		public function __construct(string $name, string $description, int $position, int $sectionId) {

			$this->description = $description;
			$this->name = $name;
			$this->position = $position;
			$this->sectionId = $sectionId;

		}


	}