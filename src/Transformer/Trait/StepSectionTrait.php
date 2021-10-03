<?php
	namespace App\Transformer\Trait;

	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;
	use App\Request\Payloads\StepPayload;
	use App\Request\Payloads\StepTemplatePayload;

	trait StepSectionTrait {

		/**
		 * @param StepInterface|SectionInterface $section
		 * @return StepInterface|SectionInterface
		 * @see StepEntityTransformer
		 *
		 * @see SectionTemplateEntityTransformer
		 * @see SectionEntityTransformer
		 * @see StepTemplateEntityTransformer
		 */
		private function checkAndSetData(StepInterface|SectionInterface $section): StepInterface|SectionInterface {

			if (!(($this->dto instanceof StepTemplatePayload) || ($this->dto instanceof StepPayload)))
				throw new \InvalidArgumentException(
					'In ' . static::class . '. Payload not instance of SectionPayload or SectionTemplatePayload.'
				);

			if (isset($this->dto->position))
				$section->setPosition($this->dto->position);

			if (isset($this->dto->name))
				$section->setName($this->dto->name);

			if (isset($this->dto->description))
				$section->setDescription($this->dto->description);

			return $section;

		}

	}