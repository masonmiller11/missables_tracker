<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\Entity\EntityInterface;

	interface EntityTransformerInterface {

		public function create(DTOInterface $dto, bool $skipValidation = false): EntityInterface;

		public function update(DTOInterface $dto, bool $skipValidation = false): EntityInterface;

		public function delete(DTOInterface $dto, bool $skipValidation = false): EntityInterface;

	}