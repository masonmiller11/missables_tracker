<?php
	namespace App\Transformer;

	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionTemplate;
	use App\Exception\ValidationException;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionTemplateRepository;
	use App\Request\Payloads\SectionTemplatePayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

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
		 * @param ValidatorInterface $validator
		 * @param SectionTemplateRequestTransformer $DTOTransformer
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param SectionTemplateRepository $sectionTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            SectionTemplateRequestTransformer $DTOTransformer,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository,
		                            SectionTemplateRepository $sectionTemplateRepository) {

			parent::__construct($entityManager, $validator);

			$this->DTOTransformer = $DTOTransformer;
			$this->playthroughTemplateRepository = $playthroughTemplateRepository;
			$this->repository = $sectionTemplateRepository;

		}

		/**
		 *
		 * @return SectionTemplate
		 */
		public function doCreateWork(): SectionTemplate {

			if (!($this->dto instanceof SectionTemplatePayload)) {
				throw new \InvalidArgumentException('SectionEntityTransformer\'s DTO not instance of SectionTemplateDTO');
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

			$sectionTemplate = $this->checkAndSetData($this->repository->find($this->id));

			if (!($sectionTemplate instanceof SectionTemplate))
				throw new \InvalidArgumentException(
					$sectionTemplate::class . ' not instance of Section Template. Does ' . $id . 'belong to a section template?'
				);

			return $sectionTemplate;

		}

	}