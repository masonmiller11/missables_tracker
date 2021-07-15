<?php
	namespace App\DTO;

	use Symfony\Component\Validator\Constraints as Assert;

	class PlaythroughDTO implements DTOInterface {

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
		public mixed $templateId;

	}