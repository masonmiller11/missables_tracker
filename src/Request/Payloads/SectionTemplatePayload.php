<?php
	namespace App\Request\Payloads;

	use Symfony\Component\Validator\Constraints as Assert;

	class SectionTemplatePayload implements PayloadInterface {

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
		public mixed $templateId;

	}