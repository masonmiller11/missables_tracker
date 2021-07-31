<?php
	namespace App\DTO\Transformer\RequestTransformer\Playthrough;

	use App\DTO\Playthrough\AbstractPlaythroughDTO;
	use App\DTO\Transformer\RequestTransformer\AbstractRequestDTOTransformer;

	abstract class AbstractPlaythroughRequestTransformer extends AbstractRequestDTOTransformer {

		/**
		 * @param AbstractPlaythroughDTO $dto
		 * @param array $data
		 *
		 * @return AbstractPlaythroughDTO
		 */
		protected function assemblePlaythroughDTO(AbstractPlaythroughDTO $dto, array $data): AbstractPlaythroughDTO {

			$dto->visibility = $data['visibility'] ?? false;
			$dto->gameID = $data['game'];
			$dto->sections = $data['sections'] ?? [];
			$dto->sectionPositions = $data['section_positions'] ?? [];
			$dto->stepPositions = $data['step_positions'] ?? [];
			$dto->name = $data['name'] ?? 'untitled';
			$dto->description = $data['description'] ?? 'no description';

			return $dto;

		}

	}