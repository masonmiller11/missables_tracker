<?php
	namespace App\DTO\Response;

	use Symfony\Component\Validator\Constraints as Assert;

	class GameResponseDTO {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $title;

		/**
		 * @Assert\DateTime(format="Y-m-d")
		 */
		public mixed $releaseDate;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $genre;

		/**
		 * @Assert\Type('array')
		 */
		public mixed $playthroughTemplates;

	}