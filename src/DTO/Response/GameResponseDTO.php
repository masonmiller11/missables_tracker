<?php
	namespace App\DTO\Response;

	use Symfony\Component\Validator\Constraints as Assert;

	class GameResponseDTO {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $title;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $releaseDate;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $genre;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $playthroughTemplates;

	}