<?php
	namespace App\DTO\Response;

	use Symfony\Component\Validator\Constraints as Assert;

	class GameResponseDTO {

		/**
		 * @Assert\NotBlank()
		 */
		public string $title;

		/**
		 * @Assert\NotBlank()
		 */
		public string $releaseDate;

		/**
		 * @Assert\NotBlank()
		 */
		public string $genre;

		/**
		 * @Assert\NotBlank()
		 */
		public array $playthroughTemplates;

	}