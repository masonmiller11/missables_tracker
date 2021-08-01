<?php
	namespace App\Transformer;

	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;

	trait StepSectionCheckDataTrait {

		/**
		 * @see SectionTemplateEntityTransformer
		 * @see SectionEntityTransformer
		 * @see StepTemplateEntityTransformer
		 * @see StepEntityTransformer
		 *
		 * @param StepInterface|SectionInterface $entity
		 * @param array $data
		 * @return StepInterface|SectionInterface
		 */
		private function checkData (StepInterface|SectionInterface $entity, array $data): StepInterface|SectionInterface {

			if (isset($data['position'])) {
				$entity->setPosition($data['position']);
			}
			if (isset($data['name'])) {
				$entity->setName($data['name']);
			}
			if (isset($data['description'])) {
				$entity->setDescription($data['description']);
			}

			return $entity;

		}

	}