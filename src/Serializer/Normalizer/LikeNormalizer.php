<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Playthrough\PlaythroughTemplateLike;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

	class LikeNormalizer implements ContextAwareNormalizerInterface {

		/**
		 * @param PlaythroughTemplateLike $object
		 * @param string|null $format
		 * @param array $context
		 * @return array
		 */
		public function normalize ($object, string $format = null, array $context = []): array {

			$data['id'] = $object->getId();

			$data['playthroughTemplate'] = [
				'template_id' => $object->getLikedTemplate()->getId(),
				'template_author' => $object->getLikedTemplate()->getOwner(),
				'template_likes' => $object->getLikedTemplate()->countLikes(),
				'game' => [
					'game_title' => $object->getLikedTemplate()->getGame()->getTitle(),
					'game_id' => $object->getLikedTemplate()->getGame()->getId()
				]
			];

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			return $data instanceof PlaythroughTemplateLike;

		}

	}