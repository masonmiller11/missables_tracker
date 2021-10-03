<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionTemplate;
	use App\Exception\InvalidEntityException;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionTemplateRepository;
	use App\Request\Payloads\SectionTemplatePayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	final class SectionTemplateEntityTransformer extends AbstractEntityTransformer {

		use StepSectionTrait;

		/**
		 * @var PlaythroughTemplateRepository
		 */
		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param SectionTemplateRepository $sectionTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository,
		                            SectionTemplateRepository $sectionTemplateRepository) {

			parent::__construct($entityManager, $sectionTemplateRepository);

			$this->playthroughTemplateRepository = $playthroughTemplateRepository;

		}

		/**
		 *
		 * @return SectionTemplate
		 */
		public function doCreateWork(): SectionTemplate {

			if (!($this->dto instanceof SectionTemplatePayload)) {
				throw new InvalidPayloadException(SectionTemplatePayload::class, $this->dto::class);
			}

			$playthroughTemplate = $this->getTemplate();

			return new SectionTemplate($this->dto->name, $this->dto->description, $playthroughTemplate, $this->dto->position);

		}

		private function getTemplate(): PlaythroughTemplate {

			$playthroughTemplate = $this->playthroughTemplateRepository->find($this->dto->templateId);

			if (!$playthroughTemplate) {
				throw new NotFoundHttpException('template not found');
			}

			return $playthroughTemplate;

		}

		/**
		 * @return SectionTemplate
		 */
		public function doUpdateWork(): SectionTemplate {

			if (!($this->repository instanceof SectionTemplateRepository))
				throw new InvalidRepositoryException(SectionTemplateRepository::class, $this->repository::class);

			$sectionTemplate = $this->checkAndSetData($this->repository->find($this->id));

			if (!($sectionTemplate instanceof SectionTemplate))
				throw new InvalidEntityException(SectionTemplate::class, $sectionTemplate::class);;

			return $sectionTemplate;

		}

	}