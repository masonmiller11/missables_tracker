<?php
	namespace App\DTO\Transformer;

	interface ResponseDTOTransformerInterface {

		public function transformFromObject($object);
		public function transformFromObjects($objects): iterable;

	}