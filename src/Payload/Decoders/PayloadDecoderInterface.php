<?php
	namespace App\Payload\Decoders;

	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;

	interface PayloadDecoderInterface {

		/**
		 * @param string $intent
		 * @param string $input
		 * @param string|null $format
		 * @return object
		 * @throws ValidationException|PayloadDecoderException
		 */
		public function parse(string $intent, string $input, ?string $format = null): object;

	}