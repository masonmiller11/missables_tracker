<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Exception\UnexpectedTypeException;
	use App\DTO\Response\PlaythroughResponseDTO;
	use App\Entity\Playthrough;

	class PlaythroughResponseDTOTransformer extends AbstractResponseDTOTransformer {

		public function transformFromObject($object) :PlaythroughResponseDTO{

			if (!$object instanceof Playthrough) {
				throw new UnexpectedTypeException('Expected type of Playthrough but got' . \get_class($object));
			}

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