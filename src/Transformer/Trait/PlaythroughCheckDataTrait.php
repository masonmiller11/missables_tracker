<?php
	namespace App\Transformer\Trait;

	use App\Entity\Playthrough\PlaythroughInterface;

	trait PlaythroughCheckDataTrait {

		/**
		 * @see PlaythroughTemplateEntityTransformer
		 * @see PlaythroughEntityTransformer
		 * @param array                $data
		 * @param PlaythroughInterface $playthrough
		 *
		 * @return PlaythroughInterface
		 */
		private function checkData (array $data, PlaythroughInterface $playthrough): PlaythroughInterface {

			if (isset($data['visibility'])) {
				$playthrough->setVisibility($data['visibility']);
			}
			if (isset($data['name'])) {
				$playthrough->setName($data['name']);
			}
			if (isset($data['description'])) {
				$playthrough->setDescription($data['description']);
			}

			return $playthrough;

		}

	}