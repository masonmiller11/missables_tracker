<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Response\PlaythroughTemplateResponseDTO;

	class PlaythroughTemplateResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return PlaythroughTemplateResponseDTO
		 */
		public function transformFromObject($object) :PlaythroughTemplateResponseDTO{

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