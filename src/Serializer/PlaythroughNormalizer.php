<?php
	namespace App\Serializer;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\Section;
	use App\Entity\Step\Step;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

	class PlaythroughNormalizer implements  ContextAwareNormalizerInterface {

		/**
		 * @param Playthrough|PlaythroughTemplate $object
		 * @param string|null $format
		 * @param array $context
		 * @return array
		 */
		public function normalize ($object, string $format = null, array $context = []): array {

			$data['title'] = $object->getName();
			$data['description'] = $object->getDescription();
			$data['id'] = $object->getId();
			$data['visibility'] = $object->isVisible();
			$data['owner'] = $object->getOwner()->getUsername();

			$data['game'] = [
				'gameId' => strval($object->getGame()->getId()),
				'gameTitle' => $object->getGame()->getTitle()
			];

			$data['sections'] = $object->getSections()->map(
				fn(Section $section) => [
					'id'=>$section->getId(),
					'name'=>$section->getName(),
					'description'=>$section->getDescription(),
					'steps'=>$section->getSteps()->map(
						fn(Step $step) => [
							'id'=>$step->getId(),
							'isCompleted'=>$step->isCompleted(),
							'name'=>$step->getName(),
							'description'=>$step->getDescription()
						]
					)->toArray()
				]
			)->toArray();

			$data['stepPositions'] = call_user_func_array("array_merge",
				$object->getSections()->map(
					fn (Section $section) =>
					$section->getSteps()->map(
						fn (Step $step) =>
						$step->getPosition()
					)->toArray()
				)->toArray());

			$data['sectionPositions'] = $object->getSections()->map(
				fn(Section $section) =>
				$section->getPosition()
			)->toArray();

			if ($object instanceof Playthrough) {
				$data['templateId'] = $object->getTemplate()->getId();
			}

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			if ($data instanceof PlaythroughTemplate || $data instanceof Playthrough) {
				return true;
			}

			return false;

		}

	}