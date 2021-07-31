<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\PlaythroughInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;

	abstract class AbstractPlaythroughEntityTransformer extends AbstractEntityTransformer {

		/**
		 * @param array                $data
		 * @param PlaythroughInterface $playthrough
		 *
		 * @return PlaythroughInterface
		 */
		protected function doUpdate (array $data, PlaythroughInterface $playthrough): PlaythroughInterface {

			if (isset($data['visibility'])) {
				$playthrough->setVisibility($data['visibility']);
			}
			if (isset($data['name'])) {
				$playthrough->setName($data['name']);
			}
			if (isset($data['description'])) {
				$playthrough->setDescription($data['description']);
			}

			$this->entityManager->persist($playthrough);
			$this->entityManager->flush();

			return $playthrough;

		}

	}