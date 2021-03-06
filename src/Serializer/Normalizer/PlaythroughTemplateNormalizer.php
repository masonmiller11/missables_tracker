<?php
	namespace App\Serializer\Normalizer;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\Step\StepTemplate;
	use App\Repository\PlaythroughRepository;
	use App\Service\IGDBHelper;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	class PlaythroughTemplateNormalizer extends AbstractPlaythroughNormalizer {

		private PlaythroughRepository $repository;

		public function __construct(IGDBHelper $IGDBHelper, PlaythroughRepository $repository) {
			$this->IGDBHelper = $IGDBHelper;
			$this->repository = $repository;
		}

		/**
		 * @param Playthrough|PlaythroughTemplate $object
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

			$data = $this->createData($object);
			$data['likes'] = $object->countLikes();

			try {
				$repositoryData = $this->repository->findAllByTemplate($object->getId(), 1, 12);
				$data['playthroughs'] = $repositoryData['totalItems'];
			} catch (NotFoundHttpException) {
				$data['playthroughs'] = 0;
			}

			if (!$context['context_flag']) {

				$data['sections'] = $object->getSections()->map(
					fn(SectionTemplate $section) => [
						'id' => $section->getId(),
						'name' => $section->getName(),
						'description' => $section->getDescription(),
						'position' => $section->getPosition(),
						'steps' => $section->getSteps()->map(
							fn(StepTemplate $step) => [
								'id' => $step->getId(),
								'name' => $step->getName(),
								'position' => $step->getPosition(),
								'description' => $step->getDescription()
							]
						)->toArray()
					]
				)->toArray();

			}

			return $data;

		}

		public function supportsNormalization($data, string $format = null, array $context = []): bool {

			return $data instanceof PlaythroughTemplate;

		}

	}