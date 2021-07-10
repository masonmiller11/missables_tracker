<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	interface ResponseDTOTransformerInterface {

		public function transformFromObject($object);
		public function transformFromObjects($objects): iterable;

	}