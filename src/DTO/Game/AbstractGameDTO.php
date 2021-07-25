<?php
	namespace App\DTO\Game;

	use Symfony\Component\Validator\Constraints as Assert;
	use App\Genre;

	abstract class AbstractGameDTO {

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
		 * @Assert\NotNull
		 * @Assert\Type("float")
		 */
		public mixed $rating;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $summary;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $storyline;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $slug;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("array")
		 */
		public mixed $screenshots;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("array")
		 */
		public mixed $platforms;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $cover;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("array")
		 */
		public mixed $artworks;

		#[Assert\Choice(callback: [Genre::class, 'values'])]
		public mixed $genre;

	}