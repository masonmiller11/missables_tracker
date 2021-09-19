<?php
	namespace App\Request\Payloads;

	use Symfony\Component\Validator\Constraints as Assert;

	class PlaythroughTemplatePayload implements PayloadInterface {

		use PayloadTrait;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\Type("bool")
		 */
		public mixed $visibility;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\Type("integer")
		 */
		public mixed $gameID;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\All({
		 *     @Assert\Type("integer")
		 * })
		 */
		public mixed $sections;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\All({
		 *     @Assert\Type("integer")
		 * })
		 * @Assert\Unique
		 */
		public mixed $stepPositions;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\All({
		 *     @Assert\Type("integer")
		 * })
		 * @Assert\Unique
		 */
		public mixed $sectionPositions;

		/**
		 * @Assert\NotBlank(groups={"create"})
		 */
		public mixed $name;

		/**
		 * @Assert\NotBlank(groups={"create"})
		 */
		public mixed $description;

	}