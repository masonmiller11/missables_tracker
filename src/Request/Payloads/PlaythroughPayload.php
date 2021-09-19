<?php

	namespace App\Request\Payloads;

	use Symfony\Component\Validator\Constraints as Assert;

	class PlaythroughPayload extends PlaythroughTemplatePayload {

		use PayloadTrait;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\Type("int")
		 */
		public mixed $templateId;

	}