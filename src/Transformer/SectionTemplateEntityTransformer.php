<?php
	namespace App\Transformer;

	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\Entity\Section\SectionTemplate;
	use App\Exception\ValidationException;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionTemplateRepository;
	use App\Transformer\Trait\StepSectionCheckDataTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class SectionTemplateEntityTransformer extends AbstractEntityTransformer {

		use StepSectionCheckDataTrait;

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

			if (!($this->dto instanceof SectionTemplateDTO)) {
				throw new \InvalidArgumentException('SectionEntityTransformer\'s DTO not instance of SectionTemplateDTO');
			}

			$playthroughTemplate = $this->playthroughTemplateRepository->find($this->dto->templateId);

			if (!$playthroughTemplate) {
				throw new NotFoundHttpException('template not found');
			}

			return new SectionTemplate($this->dto->name, $this->dto->description, $playthroughTemplate, $this->dto->position);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return SectionTemplate
		 * @throws ValidationException
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): SectionTemplate {

			$sectionTemplate = $this->repository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->templateId = $sectionTemplate->getPlaythrough()->getId();

			if (!$skipValidation) $this->validate($tempDTO);

			$sectionTemplate = $this->checkData($sectionTemplate, json_decode($request->getContent(), true));

			if (!($sectionTemplate instanceof SectionTemplate)) {
				throw new \InvalidArgumentException(
					$sectionTemplate::class . ' not instance of Section Template. Does ' . $id . 'belong to a section template?');
			}

			return $sectionTemplate;

		}

	}