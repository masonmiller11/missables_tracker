<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

	abstract class AbstractPlaythroughNormalizer implements ContextAwareNormalizerInterface {

		protected function createData ($object): array {

			$data['title'] = $object->getName();
			$data['description'] = $object->getDescription();
			$data['id'] = $object->getId();
			$data['visibility'] = $object->isVisible();
			$data['owner'] = $object->getOwner()->getUsername();

			$data['game'] = [
				'gameId' => strval($object->getGame()->getId()),
				'gameTitle' => $object->getGame()->getTitle()
			];

			$data['stepPositions'] = call_user_func_array("array_merge",
				$object->getSections()->map(
					fn (SectionInterface $section) =>
					$section->getSteps()->map(
						fn (StepInterface $step) =>
						$step->getPosition()
					)->toArray()
				)->toArray());

			$data['sectionPositions'] = $object->getSections()->map(
				fn(SectionInterface $section) =>
				$section->getPosition()
			)->toArray();

			return $data;

		}

	}