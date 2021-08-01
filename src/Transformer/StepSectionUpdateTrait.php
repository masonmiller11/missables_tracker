<?php
	namespace App\Transformer;

	use App\Entity\EntityInterface;
	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;

	trait StepSectionUpdateTrait {

		private function checkDataKeys (StepInterface|SectionInterface $entity, array $data): StepInterface|SectionInterface {

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