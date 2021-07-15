<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\GameDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Game;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use Symfony\Component\Validator\Exception\UnexpectedTypeException;

	class GameResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param Game $object
		 *
		 * @return GameDTO
		 */
		public function transformFromObject($object): GameDTO {

			if (!($object instanceof Game)) {
				throw new UnexpectedTypeException($object, 'Game');
			}

			$dto = new GameDTO();
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
			$dto->internetGameDatabaseID = $object->getInternetGameDatabaseID();
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