<?php
	namespace App\Transformer;

	use App\DTO\Step\StepTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Step\StepTemplateRequestTransformer;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\Step\StepTemplate;
	use App\Exception\ValidationException;
	use App\Repository\SectionTemplateRepository;
	use App\Repository\StepTemplateRepository;
	use App\Request\Payloads\StepTemplatePayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class StepTemplateEntityTransformer extends AbstractEntityTransformer {

		use StepSectionTrait;

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
		public function doCreateWork(): StepTemplate {

			if (!($this->dto instanceof StepTemplatePayload)) {
				throw new \InvalidArgumentException(
					'StepTemplateEntityTransformer\'s DTO not instance of StepTemplateDTO'
				);
			}

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
		 * @throws ValidationException
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): StepTemplate {

			$stepTemplate = $this->repository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->sectionTemplateId = $stepTemplate->getSection()->getId();
			if (!$skipValidation) $this->validate($tempDTO);

			$stepTemplate = $this->checkData($stepTemplate, json_decode($request->getContent(), true));

			if (!($stepTemplate instanceof StepTemplate)) {
				throw new \InvalidArgumentException(
					$stepTemplate::class . ' not instance of StepTemplate. Does ' . $id . 'belong to a step template?');
			}

			return $stepTemplate;

		}

		private function getSectionTemplate(): SectionTemplate {

			$sectionTemplate = $this->sectionTemplateRepository->find($this->dto->sectionTemplateId);

			if (!$sectionTemplate) {
				throw new NotFoundHttpException('section template not found');
			}

			return $sectionTemplate;

		}

	}