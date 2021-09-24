<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Request\Payloads\PayloadInterface;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * Interface EntityTransformerInterface
	 * @package App\Transformer
	 */
	interface EntityTransformerInterface {

		/**
		 * Eventually the type-hinted Object for $dto should be replaced by PayloadInterface
		 */
		public function create(PayloadInterface $payload, User|null $user = null): EntityInterface;

		public function update(PayloadInterface $payload, int $id): EntityInterface;

		public function delete(int $id): void;


	}