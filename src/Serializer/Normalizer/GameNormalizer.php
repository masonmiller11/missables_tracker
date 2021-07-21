<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Game;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

	class GameNormalizer implements  ContextAwareNormalizerInterface {

		/**
		 * @param Game $object
		 * @param string|null $format
		 * @param array $context
		 * @return array
		 */
		public function normalize ($object, string $format = null, array $context = []): array {

			$data['title'] = $object->getTitle();
			$data['templateCount'] = $object->getPlaythroughTemplateCount();
			$data['playthroughCount'] = $object->getPlaythroughCount();
			$data['playthroughTemplate'] = $object->getTemplates()->map(
				fn(PlaythroughTemplate $playthroughTemplate) => [
					'id'=>$playthroughTemplate->getId(),
					'visibility'=>$playthroughTemplate->isVisible(),
					'votes'=>$playthroughTemplate->getVotes(),
					'owner'=>$playthroughTemplate->getOwner()->getId(),
				]
			)->toArray();
			$data['id'] = $object->getId();
			$data['summary'] = $object->getSummary();
			$data['storyline'] = $object->getStoryline();
			$data['rating'] = $object->getRating();
			$data['slug'] = $object->getSlug();
			$data['screenshots'] = $object->getScreenshots();
			$data['cover'] = $object->getCover();
			$data['artworks'] = $object->getArtworks();
			$data['internetGameDatabaseId'] = $object->getInternetGameDatabaseID();
			$data['releaseDate'] = $object->getReleaseDate()->format('Y-m-d');

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			return $data instanceof Game;

		}

	}