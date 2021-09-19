<?php
	namespace App\Payload\Decoders;

	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\DecoderIntent;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Constraint;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class SymfonyDeserializeDecoder implements PayloadDecoderInterface {

		/**We'll use these values when assembling our validation groups.
		These should be the strings that we use in our Assert statements.*/
		public const GROUP_CREATE = 'create';
		public const GROUP_UPDATE = 'update';
		public const GROUP_CLONE = 'clone';

		/**
		 * @var ValidatorInterface|null
		 */
		private ?ValidatorInterface $validator;

		/**
		 * @var string
		 */
		private string $payloadClass;

		/**
		 * @var string
		 */
		private string $defaultFormat;

		/**
		 * @var SerializerInterface
		 */
		private SerializerInterface $serializer;

		/**
		 * @var array
		 */
		protected array $validatorGroups = [];

		/**
		 * @var array
		 */
		protected array $deserializeContext = [];

		/**
		 *
		 * SymfonyDeserializeDecoder constructor.
		 * @param SerializerInterface $serializer
		 * @param string $defaultFormat
		 * @param string $payloadClass
		 * @param ValidatorInterface|null $validator
		 * @param array|null $validatorGroups
		 */
		public function __construct(
			SerializerInterface $serializer,
			string $defaultFormat,
			string $payloadClass,
			ValidatorInterface $validator = null,
			array $validatorGroups = null
		) {
			$this->serializer = $serializer;
			$this->defaultFormat = $defaultFormat;
			$this->payloadClass = $payloadClass;
			$this->validator = $validator;

			/**
			 * We only run this if validator is not null and validator groups is null.
			 * We're essentially assembling create, clone, and update validation groups so that each one includes
			 * itself as well as the DEFAULT_GROUP. For example DecoderIntern::CLONE will be an array including itself
			 * and the DEFAULT GROUP.
			 */
			if ($validator) {
				$this->validatorGroups = $validatorGroups ?: [
					DecoderIntent::CLONE => [
						Constraint::DEFAULT_GROUP,
						static::GROUP_CLONE
					],
					DecoderIntent::CREATE => [
						Constraint::DEFAULT_GROUP,
						static::GROUP_CREATE
					],
					DecoderIntent::UPDATE => [
						Constraint::DEFAULT_GROUP,
						static::GROUP_UPDATE
					]
				];
			}

		}

		/**
		 * @throws ValidationException
		 */
		public function parse(string $intent, string $input, ?string $format = null): object {
			$payload = $this->serializer->deserialize(
				$input,
				$this->getPayloadClass(),
				$format ?? $this->getDefaultFormat(),
				$this->getDeserializeContext()
			);

			if ($this->validator) {
				$groups = $this->getValidatorGroups($intent);

				if ($groups === null)
					throw PayloadDecoderException::invalidIntent($intent);

				$errors = $this->validator->validate($payload, null, $groups);

				if (count($errors) > 0)
					throw new ValidationException($errors);

			}

			return $payload;

		}

		/**
		 * @return string
		 */
		public function getPayloadClass(): string {
			return $this->payloadClass;
		}

		/**
		 * @param string $payloadClass
		 * @return SymfonyDeserializeDecoder
		 */
		public function setPayloadClass(string $payloadClass): SymfonyDeserializeDecoder {
			$this->payloadClass = $payloadClass;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultFormat(): string {
			return $this->defaultFormat;
		}

		/**
		 * @param string $defaultFormat
		 * @return SymfonyDeserializeDecoder
		 */
		public function setDefaultFormat(string $defaultFormat): SymfonyDeserializeDecoder {
			$this->defaultFormat = $defaultFormat;

			return $this;
		}

		/**
		 * @param string $intent
		 * @return array|null
		 */
		public function getValidatorGroups(string $intent): ?array {
			return $this->validatorGroups[$intent] ?? null;
		}

		/**
		 * @param string $intent
		 * @param array  $groups
		 *
		 * @return $this
		 */
		public function setValidatorGroups(string $intent, array $groups): SymfonyDeserializeDecoder {
			$this->validatorGroups[$intent] = $groups;

			return $this;
		}

		public function getCreateGroups(): array {
			return $this->getValidatorGroups(DecoderIntent::CREATE);
		}

		/**
		 * @param string[] $createGroups
		 *
		 * @return $this
		 * @deprecated deprecated since version 1.5.1
		 *
		 */
		public function setCreateGroups(array $createGroups): SymfonyDeserializeDecoder {
			$this->setValidatorGroups(DecoderIntent::CREATE, $createGroups);

			return $this;
		}

		/**
		 * @return string[]
		 * @deprecated deprecated since version 1.5.1
		 *
		 */
		public function getUpdateGroups(): array {
			return $this->getValidatorGroups(DecoderIntent::UPDATE);
		}

		/**
		 * @param string[] $updateGroups
		 *
		 * @return $this
		 * @deprecated deprecated since version 1.5.1
		 *
		 */
		public function setUpdateGroups(array $updateGroups): SymfonyDeserializeDecoder {
			$this->setValidatorGroups(DecoderIntent::UPDATE, $updateGroups);

			return $this;
		}

		/**
		 * @return array
		 */
		public function getDeserializeContext(): array {
			return $this->deserializeContext;
		}

		/**
		 * @param array $deserializeContext
		 * @return SymfonyDeserializeDecoder
		 */
		public function setDeserializeContext(array $deserializeContext): SymfonyDeserializeDecoder {
			$this->deserializeContext = $deserializeContext;

			return $this;
		}

	}