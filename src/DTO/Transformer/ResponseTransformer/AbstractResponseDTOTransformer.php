<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	abstract class AbstractResponseDTOTransformer implements ResponseDTOTransformerInterface {

		public function transformFromObjects($objects): iterable {
			$dto = [];

			foreach ($objects as $object) {
				$dto[] = $this->transformFromObject($object);
			}

			return $dto;
		}

	}