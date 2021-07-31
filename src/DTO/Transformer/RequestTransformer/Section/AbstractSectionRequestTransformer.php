<?php
	namespace App\DTO\Transformer\RequestTransformer\Section;

	use App\DTO\Playthrough\AbstractPlaythroughDTO;
	use App\DTO\Section\AbstractSectionDTO;
	use App\DTO\Transformer\RequestTransformer\AbstractRequestDTOTransformer;
	use App\Exception\ValidationException;

	abstract class AbstractSectionRequestTransformer extends AbstractRequestDTOTransformer {

		/**
		 * @param AbstractSectionDTO $dto
		 * @param array              $data
		 *
		 * @return AbstractSectionDTO
		 */
		protected function assembleSectionDTO(AbstractSectionDTO $dto, array $data): AbstractSectionDTO {

			if (!isset($data['position'])) {
				throw new ValidationException('sections must include position');
			}

			$dto->name = $data['name'] ?? 'untitled';
			$dto->description = $data['description'] ?? 'no description';
			$dto->position = $data['position'];

			return $dto;

		}

	}