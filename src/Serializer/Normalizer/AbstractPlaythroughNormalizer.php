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
				'owner' => $object->getOwner()->getHandle(),
			];

			$data['game'] = [
				'gameID' => strval($object->getGame()->getId()),
				'gameTitle' => $object->getGame()->getTitle(),
				'cover' => $this->IGDBHelper->getAndSaveIfNeededCoverArtForGame($object->getGame())
			];

			return $data;

		}

	}