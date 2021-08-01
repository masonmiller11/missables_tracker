<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\PlaythroughInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;

	abstract class AbstractStepEntityTransformer extends AbstractEntityTransformer {

		/**
		 * @param array         $data
		 * @param StepInterface $step
		 *
		 * @return StepInterface
		 */
		protected function doUpdate (array $data, StepInterface $step): StepInterface {

			if (isset($data['position'])) {
				$step->setPosition($data['position']);
			}
			if (isset($data['name'])) {
				$step->setName($data['name']);
			}
			if (isset($data['description'])) {
				$step->setDescription($data['description']);
			}

			$this->entityManager->persist($step);
			$this->entityManager->flush();

			return $step;

		}

	}