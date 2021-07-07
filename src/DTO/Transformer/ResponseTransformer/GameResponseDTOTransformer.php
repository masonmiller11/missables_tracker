<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Exception\UnexpectedTypeException;
	use App\DTO\Response\GameResponseDTO;
	use App\Entity\Game;
	use App\Entity\PlaythroughTemplate;

	class GameResponseDTOTransformer extends AbstractResponseDTOTransformer {

		public function transformFromObject($object): GameResponseDTO {

			if (!$object instanceof Game) {
				if (!$object) {
					throw new UnexpectedTypeException('Resource not found');
				}
					throw new UnexpectedTypeException('Expected type of Game but got' . \get_class($object));
			}

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