<?php
	namespace App\Payload\Registry;

	use App\Payload\Decoders\PayloadDecoderInterface;
	use App\Payload\Registry\Factories\PayloadDecoderFactoryInterface;
	use App\Payload\Registry\Factories\SymfonyDeserializeDecoderFactory;

	class PayloadDecoderRegistry implements PayloadDecoderRegistryInterface {

		/**
		 * @var PayloadDecoderFactoryInterface
		 */
		private PayloadDecoderFactoryInterface $decoderFactory;

		/**
		 * @var array
		 */
		protected array $decoders = [];

		/**
		 * PayloadDecoderFactoryInterface has an alias set up pointing to SymfonyDeserializeDecoderFactory.
		 * Check this out yourself in payload-decoder.yaml.
		 *
		 * @See SymfonyDeserializeDecoderFactory
		 *
		 * PayloadDecoderRegistry constructor.
		 * @param PayloadDecoderFactoryInterface $decoderFactory
		 */
		public function __construct(PayloadDecoderFactoryInterface $decoderFactory) {
			$this->decoderFactory = $decoderFactory;
		}

		public function getDecoder(string $dtoClass): PayloadDecoderInterface {
			return $this->decoders[$dtoClass] ?? $this->decoders[$dtoClass] = $this->decoderFactory->create($dtoClass);
		}
	}