<?php
	namespace App\Exception;

	use JetBrains\PhpStorm\Pure;

	class InvalidRepositoryException extends \InvalidArgumentException {

		#[Pure]
		public function __construct(string $expectedRepository,
		                            string $currentRepository) {

			$message = static::class . '\'s Repository expected instance of ' . $expectedRepository . '. Got ' . $currentRepository . ' instead.';
			parent::__construct($message);

		}

	}