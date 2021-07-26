<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\Entity\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;

	interface EntityTransformerInterface {

		public function create(DTOInterface $dto, bool $skipValidation = false): EntityInterface;

		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface;

		public function delete(int $id): void;

	}