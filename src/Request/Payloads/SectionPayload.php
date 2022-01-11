<?php
	namespace App\Request\Payloads;

	use Symfony\Component\Validator\Constraints as Assert;

	class SectionPayload implements PayloadInterface {

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
		public mixed $playthroughId;

		public function __construct(string $name, string $description, int $position, int $playthroughId) {

			$this->description = $description;
			$this->name = $name;
			$this->position = $position;
			$this->playthroughId = $playthroughId;

		}

	}