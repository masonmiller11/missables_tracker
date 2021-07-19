<?php
	namespace App\Exception;

	use JetBrains\PhpStorm\Pure;

	class ValidationException extends \RuntimeException {

		private const CODE = 400;

		#[Pure]
		public function __construct(string $message) {
			parent::__construct($message, self::CODE);
		}

	}