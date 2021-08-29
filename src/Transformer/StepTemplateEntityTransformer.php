<?php
	namespace App\Transformer;

	use App\DTO\Step\StepTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Step\StepTemplateRequestTransformer;
	use App\Entity\Step\StepTemplate;
	use App\Repository\GameRepository;
	use App\Repository\SectionTemplateRepository;
	use App\Repository\StepTemplateRepository;
	use App\Transformer\Trait\StepSectionCheckDataTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class StepTemplateEntityTransformer extends AbstractEntityTransformer {

		use StepSectionCheckDataTrait;

		private SectionTemplateRepository $sectionTemplateRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param StepTemplateRequestTransformer $DTOTransformer
		 * @param SectionTemplateRepository $sectionRepository
		 * @param StepTemplateRepository $stepTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
									StepTemplateRequestTransformer $DTOTransformer,
		                            SectionTemplateRepository $sectionRepository,
									StepTemplateRepository $stepTemplateRepository) {

			parent::__construct($entityManager, $validator);

			$this->DTOTransformer = $DTOTransformer;
			$this->repository = $stepTemplateRepository;
			$this->sectionTemplateRepository = $sectionRepository;

		}

		/**
		 *
		 * @return StepTemplate
		 */
		public function doCreateWork (): StepTemplate {

			assert($this->dto instanceof StepTemplateDTO);

			$sectionTemplate = $this->sectionTemplateRepository->find($this->dto->sectionTemplateId);

			if (!$sectionTemplate) {
				throw new NotFoundHttpException('section template not found');
			}

			return new StepTemplate($this->dto->name, $this->dto->description, $sectionTemplate, $this->dto->position);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return StepTemplate
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): StepTemplate {

			$stepTemplate = $this->stepTemplateRepository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->sectionTemplateId = $stepTemplate->getSection()->getId();
			if (!$skipValidation) $this->validate($tempDTO);

			$stepTemplate = $this->checkData($stepTemplate, json_decode($request->getContent(), true));

			Assert ($stepTemplate instanceof StepTemplate);

			return $stepTemplate;

		}

	}