<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Response\PlaythroughDTO;

	class PlaythroughResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return PlaythroughDTO
		 */
		public function transformFromObject($object): PlaythroughDTO{

			$dto = new PlaythroughDTO();
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