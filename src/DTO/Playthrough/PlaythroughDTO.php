<?php
	namespace App\DTO\Playthrough;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	class PlaythroughDTO extends AbstractPlaythroughDTO {

		/**
		 * @Assert\NotNull()
		 * @Assert\Type("int")
		 */
		public mixed $templateId;

	}