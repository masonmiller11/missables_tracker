<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\PlaythroughInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionInterface;

	abstract class AbstractSectionEntityTransformer extends AbstractEntityTransformer {

		/**
		 * @param array            $data
		 * @param SectionInterface $section
		 *
		 * @return SectionInterface
		 */
		protected function doUpdate (array $data, SectionInterface $section): SectionInterface {

			if (isset($data['position'])) {
				$section->setPosition($data['position']);
			}
			if (isset($data['name'])) {
				$section->setName($data['name']);
			}
			if (isset($data['description'])) {
				$section->setDescription($data['description']);
			}

			$this->entityManager->persist($section);
			$this->entityManager->flush();

			return $section;

		}

	}