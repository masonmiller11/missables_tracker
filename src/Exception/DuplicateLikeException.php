<?php
	namespace App\Exception;

	use JetBrains\PhpStorm\Pure;

	class DuplicateLikeException extends \Exception {

		private const CODE = 400;

		#[Pure]
		public function __construct() {
			$message = 'a user cannot like one template more than once';
			parent::__construct($message, self::CODE);
		}

	}