<?php

	namespace App\Request\Payloads;

	class PlaythroughPayload extends PlaythroughTemplatePayload {

		use PayloadTrait;

		/**
		 * @Assert\NotNull(groups={"create"})
		 * @Assert\Type("int")
		 */
		public mixed $templateId;

	}