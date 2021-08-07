<?php
	namespace App\DTO\Like;

	use App\DTO\DTOInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	class LikeDTO implements DTOInterface {

		/**
		 * @Assert\NotNull
		 * @Assert\Type("integer")
		 */
		public mixed $templateID;

	}