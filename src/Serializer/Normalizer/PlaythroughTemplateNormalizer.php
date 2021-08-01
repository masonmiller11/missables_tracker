<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\Step\StepTemplate;

	class PlaythroughTemplateNormalizer extends  AbstractPlaythroughNormalizer {

		/**
		 * @param Playthrough|PlaythroughTemplate $object
		 * @param string|null $format
		 * @param array $context
		 * @return array
		 */
		public function normalize ($object, string $format = null, array $context = []): array {

			$data = $this->createData($object);

			$data['sections'] = $object->getSections()->map(
				fn(SectionTemplate $section) => [
					'id'=>$section->getId(),
					'name'=>$section->getName(),
					'description'=>$section->getDescription(),
					'position'=>$section->getPosition(),
					'steps'=>$section->getSteps()->map(
						fn(StepTemplate $step) => [
							'id'=>$step->getId(),
							'name'=>$step->getName(),
							'position'=>$step->getPosition(),
							'description'=>$step->getDescription()
						]
					)->toArray()
				]
			)->toArray();

			$data['likes'] = $object->getNumberOfLikes();

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			return $data instanceof PlaythroughTemplate;

		}

	}