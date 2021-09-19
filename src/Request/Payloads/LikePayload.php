<?php
	namespace App\Request\Payloads;

	use Symfony\Component\Validator\Constraints as Assert;

	class LikePayload implements PayloadInterface {

		use PayloadTrait;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("integer")
		 */
		public mixed $templateID;

	}