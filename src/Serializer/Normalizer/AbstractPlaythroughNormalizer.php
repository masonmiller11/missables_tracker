<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Section\SectionInterface;
	use App\Entity\Step\StepInterface;
	use App\Service\IGDBHelper;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	abstract class AbstractPlaythroughNormalizer implements ContextAwareNormalizerInterface {

		protected IGDBHelper $IGDBHelper;

		/**
		 * @throws TransportExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws ClientExceptionInterface
		 */
		protected function createData($object): array {

			$data['title'] = $object->getName();
			$data['description'] = $object->getDescription();
			$data['id'] = $object->getId();
			$data['visibility'] = $object->isVisible();
			$data['owner'] = [
				'ownerID' => $object->getOwner()->getId(),
				'owner' => $object->getOwner()->getUsername(),
			];

			$data['game'] = [
				'gameID' => strval($object->getGame()->getId()),
				'gameTitle' => $object->getGame()->getTitle(),
				'cover' => $this->IGDBHelper->getCoverArtForGame($object->getGame())
			];

			// $data['stepPositions'] = call_user_func_array("array_merge",
			// 	$object->getSections()->map(
			// 		fn(SectionInterface $section) => $section->getSteps()->map(
			// 			fn(StepInterface $step) => $step->getPosition()
			// 		)->toArray()
			// 	)->toArray());
			//
			// $data['sectionPositions'] = $object->getSections()->map(
			// 	fn(SectionInterface $section) => $section->getPosition()
			// )->toArray();

			return $data;

		}

	}