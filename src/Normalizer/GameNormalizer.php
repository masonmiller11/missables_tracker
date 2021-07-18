<?php
	namespace App\Normalizer;

	use App\Entity\Game;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use Symfony\Component\Serializer\Exception\ExceptionInterface;
	use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

	class GameNormalizer implements  ContextAwareNormalizerInterface {

		private ObjectNormalizer $normalizer;

		public function __construct (ObjectNormalizer $normalizer) {
			$this->normalizer = $normalizer;
		}

		/**
		 * @param Game $object
		 *
		 * @throws ExceptionInterface
		 */
		public function normalize ($object, string $format = null, array $context = []) {

			$data = $this->normalizer->normalize($object, $format, $context);

			$data['playthroughTemplate'] = $object->getTemplates()->map(
				fn(PlaythroughTemplate $playthroughTemplate) => [
					'id'=>$playthroughTemplate->getId(),
					'visibility'=>$playthroughTemplate->isVisible(),
					'votes'=>$playthroughTemplate->getVotes(),
					'owner'=>$playthroughTemplate->getOwner()->getId(),
				]
			)->toArray();

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {
			return $data instanceof Game;
		}

	}