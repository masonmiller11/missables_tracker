<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Response\GameResponseDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Game;
	use App\Entity\PlaythroughTemplate;

	class GameResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param Game $object
		 * @return GameResponseDTO
		 */
		public function transformFromObject($object): GameResponseDTO {

			Assert($object instanceof Game);

			$dto = new GameResponseDTO();
			$dto->genre = $object->getGenre();
			$dto->title = $object->getTitle();
			$dto->id = $object->getId();
			$dto->summary = $object->getSummary();
			$dto->rating = $object->getRating();
			$dto->storyline = $object->getStoryline();
			$dto->slug = $object->getSlug();
			$dto->screenshots = $object->getScreenshots();
			$dto->platforms = $object->getPlatforms();
			$dto->cover = $object->getCover();
			$dto->artworks = $object->getArtworks();
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