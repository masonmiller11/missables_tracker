<?php
	namespace App\Request\Payloads;

	use App\Request\Payloads\PayloadTrait;
	use Symfony\Component\Validator\Constraints as Assert;
	use JetBrains\PhpStorm\Immutable;

	class GamePayload {

		use PayloadTrait;

		/**
		 * @Assert\Type("string")
		 */
		public mixed $title;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\Type("integer")
		 */
		public mixed $internetGameDatabaseID;

		/**
		 * @Assert\Type("float")
		 */
		public mixed $rating;

		/**
		 * @Assert\Type("string")
		 */
		public mixed $summary;

		/**
		 * @Assert\Type("string")
		 */
		public mixed $storyline;

		/**
		 * @Assert\Type("string")
		 */
		public mixed $slug;

		/**
		 * @Assert\Type("array")
		 */
		public mixed $screenshots;

		/**
		 * @Assert\Type("array")
		 */
		public mixed $platforms;

		/**
		 * @Assert\Type("string")
		 */
		public mixed $cover;

		/**
		 * @Assert\Type("array")
		 */
		public mixed $artworks;
		
	}