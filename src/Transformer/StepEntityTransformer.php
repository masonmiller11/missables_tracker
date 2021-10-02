<?php
	namespace App\Transformer;

	use App\Entity\Section\Section;
	use App\Entity\Step\Step;
	use App\Exception\InvalidEntityException;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Request\Payloads\StepPayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	final class StepEntityTransformer extends AbstractEntityTransformer {

		use StepSectionTrait;

		/**
		 * @var SectionRepository
		 */
		private SectionRepository $sectionRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param SectionRepository $sectionRepository
		 * @param StepRepository $stepRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            SectionRepository $sectionRepository,
		                            StepRepository $stepRepository) {

			parent::__construct($entityManager, $stepRepository);

			$this->sectionRepository = $sectionRepository;

		}

		/**
		 *
		 * @return Step
		 */
		public function doCreateWork(): Step {

			if (!($this->dto instanceof StepPayload)) {
				throw new InvalidPayloadException(StepPayload::class, $this->dto::class);
			}

			$section = $this->getSection();

			return new Step($this->dto->name, $this->dto->description, $section, $this->dto->position);

		}

		private function getSection(): Section {

			$section = $this->sectionRepository->find($this->dto->sectionId);

			if (!$section) {
				throw new NotFoundHttpException('section not found');
			}

			return $section;

		}

		/**
		 * @return Step
		 */
		public function doUpdateWork(): Step {

			if (!($this->repository instanceof StepRepository))
				throw new InvalidRepositoryException(StepRepository::class, $this->repository::class);

			$step = $this->checkAndSetData($this->repository->find($this->id));

			if (!($step instanceof Step))
				throw new InvalidEntityException(Step::class, $step::class);

			return $step;

		}

	}