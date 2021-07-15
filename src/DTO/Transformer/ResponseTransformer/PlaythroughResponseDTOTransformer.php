<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Playthrough\PlaythroughDTO;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Section\Section;
	use App\Entity\Step\Step;
	use Symfony\Component\Validator\Exception\UnexpectedTypeException;

	class PlaythroughResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return PlaythroughDTO
		 * @throws \Exception
		 */
		public function transformFromObject($object): PlaythroughDTO{

			if (!($object instanceof Playthrough)) {
				throw new UnexpectedTypeException($object, 'Playthrough');
			}

			$dto = new PlaythroughDTO();
			$dto->id = $object->getId();
			$dto->visibility = $object->isVisible();
			$dto->owner = $object->getOwner()->getUsername();
			$dto->templateId = $object->getTemplate()->getId();
			$dto->game = [
				'id' => strval($object->getGame()->getId()),
				'title' => $object->getGame()->getTitle()
			];
			$dto->sections = $object->getSections()->map(
				fn(Section $section) => [
					'id'=>$section->getId(),
					'name'=>$section->getName(),
					'description'=>$section->getDescription(),
					'steps'=>$section->getSteps()->map(
						fn(Step $step) => [
							'id'=>$step->getId(),
							'isCompleted'=>$step->isCompleted(),
							'name'=>$step->getName(),
							'description'=>$step->getDescription()
						]
					)->toArray()
				]
			)->toArray();

			return $dto;



		}

	}