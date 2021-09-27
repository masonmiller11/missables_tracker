<?php
	namespace App\Transformer\Trait;

	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;
	use App\Request\Payloads\SectionPayload;
	use App\Request\Payloads\SectionTemplatePayload;

	trait StepSectionTrait {

		/**
		 * @param StepInterface|SectionInterface $section
		 * @return StepInterface|SectionInterface
		 *@see StepEntityTransformer
		 *
		 * @see SectionTemplateEntityTransformer
		 * @see SectionEntityTransformer
		 * @see StepTemplateEntityTransformer
		 */
		private function checkAndSetData (StepInterface|SectionInterface $section): StepInterface|SectionInterface {

			if (!(($this->dto instanceof SectionTemplatePayload) || ($this->dto instanceof SectionPayload)))
				throw new \InvalidArgumentException(
					'In ' . static::class . '. Payload not instance of SectionPayload or SectionTemplatePayload.'
				);

			$this->dto->position ?? $section->setPosition($this->dto->position);

			$this->dto->name ?? $section->setName($this->dto->name);

			$this->dto->description ?? $section->setDescription($this->dto->description);

			return $section;

		}

	}