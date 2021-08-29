<?php
	namespace App\Transformer;

	use App\DTO\Step\StepDTO;
	use App\DTO\Transformer\RequestTransformer\Step\StepRequestTransformer;
	use App\Entity\Step\Step;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Transformer\Trait\StepSectionCheckDataTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class StepEntityTransformer extends AbstractEntityTransformer {

		use StepSectionCheckDataTrait;

		/**
		 * @var SectionRepository
		 */
		private SectionRepository $sectionRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param StepRequestTransformer $DTOTransformer
		 * @param SectionRepository $sectionRepository
		 * @param StepRepository $stepRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
									StepRequestTransformer $DTOTransformer,
		                            SectionRepository $sectionRepository,
									StepRepository $stepRepository) {

			parent::__construct($entityManager, $validator);

			$this->DTOTransformer = $DTOTransformer;
			$this->sectionRepository = $sectionRepository;
			$this->repository = $stepRepository;

		}

		/**
		 *
		 * @return Step
		 */
		public function doCreateWork (): Step {

			if (!($this->dto instanceof StepDTO)) {

			$section = $this->sectionRepository->find($this->dto->sectionId);

			if (!$section) {
				throw new NotFoundHttpException('section not found');
			}

			return new Step($this->dto->name, $this->dto->description, $section, $this->dto->position);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return Step
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): Step {

			$step = $this->repository->find($id);


			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->sectionId = $step->getSection()->getId();
			if (!$skipValidation) $this->validate($tempDTO);

			$step = $this->checkData($step,json_decode($request->getContent(), true));

			if (!($step instanceof Step)) {

			return $step;

		}

	}