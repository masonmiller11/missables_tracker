<?php
	namespace App\Request\Payloads;

	class GameIGDBPayload {

		use PayloadTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Type("string")
		 */
		public mixed $title;

		/**
		 * @Assert\NotNull()
		 * @Assert\Type("integer")
		 */
		public mixed $internetGameDatabaseID;

		/**
		 * @Assert\Type("float")
		 */
		public mixed $rating;

		/**
		 * @Assert\NotBlank()
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
		 * @Assert\NotBlank()
		 * @Assert\Type("string")
		 */
		public mixed $cover;

		/**
		 * @Assert\Type("array")
		 */
		public mixed $artworks;

	}