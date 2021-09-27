<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Section\Section;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidEntityException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\PlaythroughRepository;
	use App\Repository\SectionRepository;
	use App\Request\Payloads\SectionPayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	final class SectionEntityTransformer extends AbstractEntityTransformer {

		use StepSectionTrait;

		/**
		 * @var PlaythroughRepository
		 */
		private PlaythroughRepository $playthroughRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param PlaythroughRepository $playthroughRepository
		 * @param SectionRepository $sectionRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            PlaythroughRepository $playthroughRepository,
		                            SectionRepository $sectionRepository) {

			parent::__construct($entityManager, $sectionRepository);

			$this->playthroughRepository = $playthroughRepository;

		}

		/**
		 *
		 * @return Section
		 */
		public function doCreateWork(): Section {

			if (!($this->dto instanceof SectionPayload)) {
				throw new InvalidPayloadException(SectionPayload::class, $this->dto::class);
			}

			$playthrough = $this->getPlaythrough();

			return new Section($this->dto->name, $this->dto->name, $playthrough, $this->dto->position);

		}

		/**
		 * @return Playthrough
		 */
		private function getPlaythrough(): Playthrough {
			$playthrough = $this->playthroughRepository->find($this->dto->playthroughId);

			if (!$playthrough) {
				throw new NotFoundHttpException('playthrough not found');
			}

			return $playthrough;
		}

		/**
		 * @return Section
		 */
		public function doUpdateWork(): Section {

			if (!($this->repository instanceof SectionRepository))
				throw new InvalidRepositoryException(SectionRepository::class, $this->repository::class);

			$section = $this->checkAndSetData($this->repository->find($this->id));

			if (!($section instanceof Section))
				throw new InvalidEntityException(Section::class, $section::class);

			return $section;

		}

	}