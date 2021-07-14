<?php
	namespace App\DTO\Exception;

	use JetBrains\PhpStorm\Pure;

	class ResourceNotFoundExceptionTypeException extends \RuntimeException {

		private const CODE = 404;

		#[Pure]
		public function __construct(string $message)
		{
			parent::__construct($message, self::CODE);
		}

	}