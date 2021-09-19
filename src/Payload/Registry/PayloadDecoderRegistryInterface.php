<?php
	namespace App\Payload\Registry;

	use App\Payload\Decoders\PayloadDecoderInterface;

	interface PayloadDecoderRegistryInterface {

		/**
		 * @param string $dtoClass
		 * @return PayloadDecoderInterface
		 */
		public function getDecoder(string $dtoClass): PayloadDecoderInterface;

	}