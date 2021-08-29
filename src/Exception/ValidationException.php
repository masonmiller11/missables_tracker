<?php
	namespace App\Exception;

	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\Validator\ConstraintViolationListInterface;

	class ValidationException extends \Exception {

		private const CODE = 400;

		private ConstraintViolationListInterface $violations;

		#[Pure]
		public function __construct(ConstraintViolationListInterface $violations) {
			$message = 'Validation failed.';
			$this->violations = $violations;
			parent::__construct($message, self::CODE);
		}

		public function getViolations(): ConstraintViolationListInterface {
			return $this->violations;
		}

	}