<?php
	namespace App\Exception;

	use JetBrains\PhpStorm\Pure;

	class DuplicateResourceException extends \Exception {

		private const CODE = 400;

		#[Pure]
		public function __construct($message) {
			parent::__construct($message, self::CODE);
		}

	}