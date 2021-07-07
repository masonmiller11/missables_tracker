<?php
	namespace App\DTO\Response;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	class GameResponseDTO implements DTOInterface {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $title;

		/**
		 * @Assert\NotNull
		 * @Assert\DateTime(format="Y-m-d")
		 */
		public mixed $releaseDate;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $genre;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("array")
		 */
		public mixed $playthroughTemplates;

	}