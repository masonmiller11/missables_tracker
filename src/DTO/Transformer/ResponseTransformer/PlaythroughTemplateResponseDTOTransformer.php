<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\Step\StepTemplate;
	use Symfony\Component\Validator\Exception\UnexpectedTypeException;

	class PlaythroughTemplateResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return PlaythroughTemplateDTO
		 */
		public function transformFromObject($object) :PlaythroughTemplateDTO{

			if (!($object instanceof PlaythroughTemplate)) {
				throw new UnexpectedTypeException($object, 'PlaythroughTemplate');
			}

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
			$dto->sections = $object->getSections()->map(
				fn(SectionTemplate $sectionTemplate) => [
					'id'=>$sectionTemplate->getId(),
					'name'=>$sectionTemplate->getName(),
					'description'=>$sectionTemplate->getDescription(),
					'step_templates'=>$sectionTemplate->getSteps()->map(
						fn(StepTemplate $stepTemplate) => [
							'id'=>$stepTemplate->getId(),
							'name'=>$stepTemplate->getName(),
							'description'=>$stepTemplate->getDescription()
						]
					)->toArray()
				]
			)->toArray();

			return $dto;

		}

	}