<?php
	namespace App\Request\Payloads;

	class LikePayload {

		use PayloadTrait;

		/**
		 * @Assert\NotNull
		 * @Assert\Type("integer")
		 */
		public mixed $templateID;

	}