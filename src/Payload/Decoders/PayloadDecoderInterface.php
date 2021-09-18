<?php
	namespace App\Payload\Decoders;

	interface PayloadDecoderInterface {

		public function parse(string $intent, string $input, ?string $format = null): object;

	}