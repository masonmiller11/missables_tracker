<?php
	namespace App\Payload\Decoders;

	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Request\Payloads\PayloadInterface;

	interface PayloadDecoderInterface {

		/**
		 * @param string $intent
		 * @param string $input
		 * @param string|null $format
		 * @return PayloadInterface
		 * @throws ValidationException
		 * @throws PayloadDecoderException
		 */
		public function parse(string $intent, string $input, ?string $format = null): PayloadInterface;

	}