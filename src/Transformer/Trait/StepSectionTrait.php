<?php
	namespace App\Transformer\Trait;

	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;
	use App\Request\Payloads\SectionPayload;
	use App\Request\Payloads\SectionTemplatePayload;
	use App\Request\Payloads\StepPayload;
	use App\Request\Payloads\StepTemplatePayload;

	trait StepSectionTrait {

		/**
		 * @param StepInterface|SectionInterface $sectionOrStep
		 *
		 * @return StepInterface|SectionInterface
		 * @see StepEntityTransformer
		 *
		 * @see SectionTemplateEntityTransformer
		 * @see SectionEntityTransformer
		 * @see StepTemplateEntityTransformer
		 */
		private function checkAndSetData(StepInterface|SectionInterface $sectionOrStep): StepInterface|SectionInterface {

			if (!(($this->dto instanceof StepTemplatePayload) || ($this->dto instanceof StepPayload)
				|| ($this->dto instanceof SectionTemplatePayload) || ($this->dto instanceof SectionPayload)  ))
				throw new \InvalidArgumentException(
					'In ' . static::class . '. Payload not instance of SectionPayload or SectionTemplatePayload.'
				);

			if (isset($this->dto->position))
				$sectionOrStep->setPosition($this->dto->position);

			if (isset($this->dto->name))
				$sectionOrStep->setName($this->dto->name);

			if (isset($this->dto->description))
				$sectionOrStep->setDescription($this->dto->description);

			return $sectionOrStep;

		}

	}