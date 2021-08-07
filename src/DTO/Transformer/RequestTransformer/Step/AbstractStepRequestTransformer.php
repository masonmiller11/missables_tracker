<?php
	namespace App\DTO\Transformer\RequestTransformer\Step;

	use App\DTO\Section\AbstractSectionDTO;
	use App\DTO\Step\AbstractStepDTO;
	use App\DTO\Transformer\RequestTransformer\AbstractRequestDTOTransformer;
	use App\Exception\ValidationException;

	abstract class AbstractStepRequestTransformer extends AbstractRequestDTOTransformer {

		/**
		 * @param AbstractStepDTO $dto
		 * @param array           $data
		 *
		 * @return AbstractStepDTO
		 */
		protected function assembleStepDTO(AbstractStepDTO $dto, array $data): AbstractStepDTO {
			if (!isset($data['position'])) {
				throw new ValidationException('steps must include position');
			}

			$dto->name = $data['name'] ?? 'untitled';
			$dto->description = $data['description'] ?? 'no description';
			$dto->position = $data['position'];

			return $dto;

		}

	}