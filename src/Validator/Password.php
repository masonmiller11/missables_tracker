<?php
	namespace App\Validator;

	use Symfony\Component\Validator\Constraint;

	/**
	 * @Annotation
	 */
	class Password extends Constraint {

		public string $message = 'Passwords must be at 8 characters in length and contain at least 1 number';

	}