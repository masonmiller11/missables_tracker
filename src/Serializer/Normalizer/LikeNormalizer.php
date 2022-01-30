<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Playthrough\PlaythroughTemplateLike;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

	class LikeNormalizer implements ContextAwareNormalizerInterface {

		/**
		 * @param PlaythroughTemplateLike $object
		 * @param string|null $format
		 * @param array $context
		 * @return array
		 */
		#[Pure]
		public function normalize ($object, string $format = null, array $context = []): array {

			$data['id'] = $object->getId();

			$data['template'] = [
				'id' => $object->getLikedTemplate()->getId(),
				'name' => $object->getLikedTemplate()->getName(),
				'description' => $object->getLikedTemplate()->getDescription(),
				'game' => [
					'title' => $object->getLikedTemplate()->getGame()->getTitle(),
					'id' => $object->getLikedTemplate()->getGame()->getId()
				]
			];

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			return $data instanceof PlaythroughTemplateLike;

		}

	}