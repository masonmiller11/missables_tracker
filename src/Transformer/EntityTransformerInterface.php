<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * Interface EntityTransformerInterface
	 * @package App\Transformer
	 */
	interface EntityTransformerInterface {

		public function create(DTOInterface $dto, User $user, bool $skipValidation = false): EntityInterface;

		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface;

		public function delete(int $id): void;

	}