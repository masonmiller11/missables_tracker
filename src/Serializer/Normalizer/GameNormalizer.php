<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Game;
	use App\Service\IGDBHelper;
	use Symfony\Component\HttpClient\Exception\ClientException;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	class GameNormalizer implements ContextAwareNormalizerInterface {

		private IGDBHelper $IGDBHelper;

		public function __construct(IGDBHelper $IGDBHelper) {
			$this->IGDBHelper = $IGDBHelper;
		}

		/**
		 * @param Game $object
		 * @param string|null $format
		 * @param array $context
		 * @return array
		 * @throws ClientExceptionInterface
		 * @throws DecodingExceptionInterface
		 * @throws RedirectionExceptionInterface
		 * @throws ServerExceptionInterface
		 * @throws TransportExceptionInterface
		 */
		public function normalize($object, string $format = null, array $context = []): array {

			$data['title'] = $object->getTitle();

			try {
				$data['cover'] = $this->IGDBHelper->getCoverArtForGame($object);
			} catch (ClientException $exception) {
				$data['cover'] = 'cover unavailable';
			}
			$data['templateCount'] = $object->getPlaythroughTemplateCount();
			$data['playthroughCount'] = $object->getPlaythroughCount();
			$data['id'] = $object->getId();
			$data['summary'] = $object->getSummary();
			$data['storyline'] = $object->getStoryline();
			$data['rating'] = $object->getRating();
			$data['slug'] = $object->getSlug();
			$data['screenshots'] = $object->getScreenshots();
			$data['artworks'] = $object->getArtworks();
			$data['internetGameDatabaseId'] = $object->getInternetGameDatabaseID();
			$data['releaseDate'] = $object->getReleaseDate()->format('Y-m-d');

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			return $data instanceof Game;

		}

	}