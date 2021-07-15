<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Playthrough\PlaythroughTemplateDTO;

	class PlaythroughTemplateResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return PlaythroughTemplateDTO
		 */
		public function transformFromObject($object) :PlaythroughTemplateDTO{

			$dto = new PlaythroughTemplateDTO();
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