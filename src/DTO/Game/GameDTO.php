<?php
	namespace App\DTO\Game;

	use Symfony\Component\Validator\Constraints as Assert;
	use App\DTO\DTOInterface;

	final class GameDTO extends AbstractGameDTO implements DTOInterface {

		/**
		 * @Assert\Type("integer")
		 */
		public mixed $id;

		/**
		 * @Assert\NotNull
		 * @Assert\DateTime(format="Y-m-d")
		 */
		public mixed $releaseDate;


		/**
		 * @Assert\NotNull
		 * @Assert\Type("array")
		 */
		public mixed $playthroughTemplates;

	}