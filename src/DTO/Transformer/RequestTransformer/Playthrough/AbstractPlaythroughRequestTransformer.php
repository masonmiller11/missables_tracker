<?php
	namespace App\DTO\Transformer\RequestTransformer\Playthrough;

	use App\DTO\Playthrough\AbstractPlaythroughEntity;
	use App\DTO\Transformer\RequestTransformer\AbstractRequestDTOTransformer;

	abstract class AbstractPlaythroughRequestTransformer extends AbstractRequestDTOTransformer {

		/**
		 * @param AbstractPlaythroughEntity $dto
		 * @param array                     $data
		 *
		 * @return AbstractPlaythroughEntity
		 */
		protected function assemblePlaythroughDTO(AbstractPlaythroughEntity $dto, array $data): AbstractPlaythroughEntity {

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