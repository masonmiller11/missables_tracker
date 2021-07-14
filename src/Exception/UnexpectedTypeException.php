<?php
	namespace App\Exception;

	use JetBrains\PhpStorm\Pure;

	class UnexpectedTypeException extends \RuntimeException {

		private const CODE = 113;

		#[Pure]
		public function __construct(string $message)
		{
			parent::__construct($message, self::CODE);
		}

	}