<?php

	namespace App\DTO\Game;

	use Symfony\Component\Validator\Constraints as Assert;
	use App\DTO\DTOInterface;

	final class IGDBGameResponseDTO extends AbstractGameDTO implements DTOInterface {

		/**
		 * @Assert\NotBlank()
		 */
		public mixed $releaseDate;

	}