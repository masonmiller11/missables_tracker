<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Response\GameResponseDTO;
	use App\Entity\PlaythroughTemplate;

	class GameResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 * @return GameResponseDTO
		 */
		public function transformFromObject($object): GameResponseDTO {

			$dto = new GameResponseDTO();
			$dto->genre = $object->getGenre();
			$dto->title = $object->getTitle();
			$dto->releaseDate = $object->getReleaseDate()->format('Y-m-d');
			$dto->playthroughTemplates = $object->getTemplates()->map(
				fn(PlaythroughTemplate $playthroughTemplate) => [
					'id'=>$playthroughTemplate->getId(),
					'visibility'=>$playthroughTemplate->isVisible(),
					'votes'=>$playthroughTemplate->getVotes(),
					'owner'=>$playthroughTemplate->getOwner()->getId(),
				]
			)->toArray();
			return $dto;

		}

	}