<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Response\PlaythroughResponseDTO;

	class PlaythroughResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 * @return PlaythroughResponseDTO
		 */
		public function transformFromObject($object) :PlaythroughResponseDTO{

			$dto = new PlaythroughResponseDTO();
			$dto->id = $object->getId();
			$dto->visibility = $object->isVisible();
			$dto->owner = $object->getOwner()->getUsername();
			$dto->templateId = $object->getTemplate()->getId();
			$dto->game = [
				'id' => strval($object->getGame()->getId()),
				'title' => $object->getGame()->getTitle()
			];

			return $dto;

		}

	}