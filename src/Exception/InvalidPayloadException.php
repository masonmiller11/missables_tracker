<?php
	namespace App\Exception;

	use JetBrains\PhpStorm\Pure;

	class InvalidPayloadException extends \InvalidArgumentException {

		#[Pure]
		public function __construct(string $expectedClass,
		                            string $currentClass) {

			$message = $currentClass . ' not instance of ' . $expectedClass . '.';
			parent::__construct($message);

		}

	}