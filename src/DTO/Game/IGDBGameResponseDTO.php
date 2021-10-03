<?php

	namespace App\DTO\Game;

	use Symfony\Component\Validator\Constraints as Assert;
	use App\DTO\DTOInterface;

	final class IGDBGameResponseDTO implements DTOInterface {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $title;

		/**
		 * @Assert\NotNull
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

		//TODO eventually we want to save the cover's URL so we aren't constantly pinging IGDB
		/**
		 * @Assert\NotBlank()
		 */
		public mixed $cover;

		/**
		 * @Assert\Type("array")
		 */
		public mixed $artworks;

		/**
		 * @Assert\Type("array")
		 */
		public mixed $genres;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $releaseDate;

	}