<?php
	namespace App\DTO\Request;

	use Symfony\Component\Validator\Constraints as Assert;
	use App\Genre;

	class GameRequestDTO {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $title;

		/**
		 * @Assert\DateTime(format="Y-m-d")
		 */
		public mixed $releaseDate;

		/**
		 * @Assert\Choice(callback: [Genre::class, 'values'], message = "Choose a valid genre.")]
		 */
		public mixed $genre;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $developer;

	}