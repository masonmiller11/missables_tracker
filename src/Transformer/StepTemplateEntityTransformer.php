<?php
	namespace App\Transformer;

	use App\Entity\Section\SectionTemplate;
	use App\Entity\Step\StepTemplate;
	use App\Exception\InvalidEntityException;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\SectionTemplateRepository;
	use App\Repository\StepTemplateRepository;
	use App\Request\Payloads\StepTemplatePayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	final class StepTemplateEntityTransformer extends AbstractEntityTransformer {

		use StepSectionTrait;

		private SectionTemplateRepository $sectionTemplateRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param SectionTemplateRepository $sectionRepository
		 * @param StepTemplateRepository $stepTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            SectionTemplateRepository $sectionRepository,
		                            StepTemplateRepository $stepTemplateRepository) {

			parent::__construct($entityManager, $stepTemplateRepository);

			$this->sectionTemplateRepository = $sectionRepository;

		}

		/**
		 *
		 * @return StepTemplate
		 */
		public function doCreateWork(): StepTemplate {

			if (!($this->dto instanceof StepTemplatePayload)) {
				throw new InvalidPayloadException(StepTemplatePayload::class, $this->dto::class);

			}

			$sectionTemplate = $this->getSectionTemplate();

			if (!$sectionTemplate) {
				throw new NotFoundHttpException('section template not found');
			}

			return new StepTemplate($this->dto->name, $this->dto->description, $sectionTemplate, $this->dto->position);

		}

		/**
		 * @return StepTemplate
		 */
		public function doUpdateWork(): StepTemplate {

			if (!($this->repository instanceof StepTemplateRepository))
				throw new InvalidRepositoryException(StepTemplateRepository::class, $this->repository::class);

			$stepTemplate = $this->checkAndSetData($this->repository->find($this->id));

			if (!($stepTemplate instanceof StepTemplate))
				throw new InvalidEntityException(StepTemplate::class, $stepTemplate::class);
			
			return $stepTemplate;

		}

		private function getSectionTemplate(): SectionTemplate {

			$sectionTemplate = $this->sectionTemplateRepository->find($this->dto->sectionTemplateId);

			if (!$sectionTemplate) {
				throw new NotFoundHttpException('Section template not found');
			}

			return $sectionTemplate;

		}

	}