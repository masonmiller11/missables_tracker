<?php

	namespace App\DTO\Response;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	class IGDBResponseDTO implements DTOInterface {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $title;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("integer")
		 */
		public mixed $id;

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
		public mixed $releaseDate;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $cover;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("array")
		 */
		public mixed $artworks;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $genre;

	}