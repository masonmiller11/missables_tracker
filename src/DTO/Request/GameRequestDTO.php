<?php

	namespace App\DTO\Request;

	use Symfony\Component\Validator\Constraints as Assert;

	class GameRequestDTO {

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
		public mixed $developer;

	}
