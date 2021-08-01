<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\PlaythroughInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionInterface;

	abstract class AbstractSectionEntityTransformer extends AbstractEntityTransformer {

		use StepSectionUpdateTrait;

		/**
		 * @param array            $data
		 * @param SectionInterface $section
		 *
		 * @return SectionInterface
		 */
		protected function doUpdate (array $data, SectionInterface $section): SectionInterface {

			$section = $this->checkDataKeys($section, $data);

			$this->entityManager->persist($section);
			$this->entityManager->flush();

			return $section;

		}

	}