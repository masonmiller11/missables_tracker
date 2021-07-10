<?php
	namespace App\DTO\Request;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use App\Genre;

	class GameRequestDTO implements DTOInterface {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $title;

		/**
		 * @Assert\NotNull 
		 * @Assert\DateTime(format="Y-m-d")
		 */
		public mixed $releaseDate;

		#[Assert\Choice(callback: [Genre::class, 'values'])]
		public mixed $genre;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $developer;

	}