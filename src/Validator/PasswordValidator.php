<?php
	namespace App\Validator;

	use Symfony\Component\Validator\Constraint;
	use Symfony\Component\Validator\ConstraintValidator;
	use Symfony\Component\Validator\Exception\UnexpectedTypeException;

	class PasswordValidator extends ConstraintValidator {

		public function validate($value, Constraint $constraint) {

			if (!$constraint instanceof Password) {
				throw new UnexpectedTypeException($constraint, Password::class);
			}

			if (null === $value || '' === $value) {
				return;
			}

			if (!is_string($value)) {
				throw new UnexpectedTypeException($value, 'string');
			}

			if (!preg_match('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$', $value, $matches)) {
				$this->context->buildViolation($constraint->message)
					->setParameter('{{ string }}', $value)
					->addViolation();
			}

		}

	}