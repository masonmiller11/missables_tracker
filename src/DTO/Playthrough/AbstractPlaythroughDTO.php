<?php
	namespace App\DTO\Playthrough;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\Validator\Mapping\ClassMetadata;

	abstract class AbstractPlaythroughDTO implements DTOInterface {

		/**
		 * @Assert\NotNull()
		 * @Assert\Type("bool")
		 */
		public mixed $visibility;

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $ownerID;

		/**
		 * @Assert\NotNull,
		 * @Assert\Type("integer")
		 */
		public mixed $gameID;

		/**
		 * @Assert\All({
		 *     @Assert\NotNull,
		 *     @Assert\Type("integer")
		 * })
		 */
		public mixed $sections;

		/**
		 * @Assert\All({
		 *     @Assert\NotNull,
		 *     @Assert\Type("integer")
		 * })
		 * @Assert\Unique
		 */
		public mixed $stepPositions;

		/**
		 * @Assert\All({
		 *     @Assert\NotNull,
		 *     @Assert\Type("integer")
		 * })
		 * @Assert\Unique
		 */
		public mixed $sectionPositions;

		/**
		 * @Assert\NotBlank ()
		 */
		public mixed $name;

		/**
		 * @Assert\NotBlank ()
		 */
		public mixed $description;

	}