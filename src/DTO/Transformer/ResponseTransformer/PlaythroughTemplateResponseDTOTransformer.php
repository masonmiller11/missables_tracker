<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Exception\UnexpectedTypeException;
	use App\DTO\Response\PlaythroughTemplateResponseDTO;
	use App\Entity\PlaythroughTemplate;

	class PlaythroughTemplateResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return PlaythroughTemplateResponseDTO|null
		 */
		public function transformFromObject($object) :?PlaythroughTemplateResponseDTO{

			if (!$object instanceof PlaythroughTemplate) {
				throw new UnexpectedTypeException('Expected type of Playthrough but got' . \get_class($object));
			}

			$dto = new PlaythroughTemplateResponseDTO();
			$dto->id = $object->getId();
			$dto->visibility = $object->isVisible();
			$dto->owner = $object->getOwner()->getUsername();
			$dto->votes = $object->getVotes();
			$dto->howManyPlaythroughs = count($object->getPlaythroughs());
			$dto->game = [
				'id' => strval($object->getGame()->getId()),
				'title' => $object->getGame()->getTitle()
			];

			return $dto;

		}

	}