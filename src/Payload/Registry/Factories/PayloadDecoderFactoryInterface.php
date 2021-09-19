<?php
	namespace App\Payload\Registry\Factories;

	use App\Payload\Decoders\PayloadDecoderInterface;

	interface PayloadDecoderFactoryInterface {

		/**
		 * @param string $dtoClass
		 * @return PayloadDecoderInterface
		 */
		public function create(string $dtoClass): PayloadDecoderInterface;

	}