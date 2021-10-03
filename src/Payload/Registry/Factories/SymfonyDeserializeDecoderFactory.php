<?php
	namespace App\Payload\Registry\Factories;

	use App\Payload\Decoders\PayloadDecoderInterface;
	use App\Payload\Decoders\SymfonyDeserializeDecoder;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class SymfonyDeserializeDecoderFactory implements PayloadDecoderFactoryInterface {

		/**
		 * @var SerializerInterface
		 */
		protected SerializerInterface $serializer;

		/**
		 * @var string
		 */
		protected string $defaultFormat;

		/**
		 * @var ValidatorInterface|null
		 */
		protected ?ValidatorInterface $validator;

		/**
		 * $defaultFormat is bound in payload-decoder.yaml as 'json'.
		 *
		 * SymfonyDeserializeDecoderFactory constructor.
		 * @param SerializerInterface $serializer
		 * @param string $defaultFormat
		 * @param ValidatorInterface|null $validator
		 */
		public function __construct(
			SerializerInterface $serializer,
			string $defaultFormat,
			ValidatorInterface $validator = null
		) {
			$this->serializer = $serializer;
			$this->defaultFormat = $defaultFormat;
			$this->validator = $validator;
		}

		/**
		 * @param string $dtoClass
		 * @return PayloadDecoderInterface
		 */
		#[Pure] public function create(string $dtoClass): PayloadDecoderInterface {
			return new SymfonyDeserializeDecoder($this->serializer, $this->defaultFormat, $dtoClass, $this->validator);
		}
	}