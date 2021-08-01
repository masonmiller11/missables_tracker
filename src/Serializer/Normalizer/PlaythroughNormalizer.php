<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\Section;
	use App\Entity\Step\Step;

	class PlaythroughNormalizer extends  AbstractPlaythroughNormalizer {

		/**
		 * @param Playthrough|PlaythroughTemplate $object
		 * @param string|null $format
		 * @param array $context
		 * @return array
		 */
		public function normalize ($object, string $format = null, array $context = []): array {

			$data = $this->createData($object);

			$data['templateId'] = $object->getTemplateId();

			$data['sections'] = $object->getSections()->map(
				fn(Section $section) => [
					'id'=>$section->getId(),
					'name'=>$section->getName(),
					'position'=>$section->getPosition(),
					'description'=>$section->getDescription(),
					'steps'=>$section->getSteps()->map(
						fn(Step $step) => [
							'id'=>$step->getId(),
							'isCompleted'=>$step->isCompleted(),
							'position'=>$step->getPosition(),
							'name'=>$step->getName(),
							'description'=>$step->getDescription()
						]
					)->toArray()
				]
			)->toArray();

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			return $data instanceof Playthrough;

		}

	}