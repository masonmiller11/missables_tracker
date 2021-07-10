<?php
	namespace App\DTO\Response;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	class PlaythroughTemplateDTO implements DTOInterface {

		/**
		 * @Assert\NotNull()
		 * @Assert\Type("int")
		 */
		public mixed $id;

		/**
		 * @Assert\NotNull()
		 * @Assert\Type("bool")
		 */
		public mixed $visibility;

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $owner;

		/**
		 * @Assert\NotNull()
		 * @Assert\All({
		 *     @Assert\NotBlank,
		 *     @Assert\Type("string")
		 * })
		 */
		public mixed $game;

		/**
		 * @Assert\NotNull()
		 * @Assert\Type("int")
		 */
		public mixed $howManyPlaythroughs;

		/**
		 * @Assert\NotNull()
		 * @Assert\Type("int")
		 */
		public mixed $votes;

	}