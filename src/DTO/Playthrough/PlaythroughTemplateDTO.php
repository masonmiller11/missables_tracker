<?php
	namespace App\DTO\Playthrough;

	use Symfony\Component\Validator\Constraints as Assert;

	class PlaythroughTemplateDTO extends AbstractPlaythroughDTO {

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