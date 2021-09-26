<?php
	namespace App\Transformer;

	use App\DTO\Step\StepDTO;
	use App\DTO\Transformer\RequestTransformer\Step\StepRequestTransformer;
	use App\Entity\Section\Section;
	use App\Entity\Step\Step;
	use App\Exception\ValidationException;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Request\Payloads\StepPayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

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

			if (!($this->dto instanceof StepPayload)) {
				throw new \InvalidArgumentException('StepEntityTransformer\'s DTO not instance of StepDTO');
			}

			$section = $this->getSection();

			return new Step($this->dto->name, $this->dto->description, $section, $this->dto->position);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return Step
		 * @throws ValidationException
		 */
		public function doUpdateWork(): Step {

			$step = $this->repository->find($this->id);

			$step = $this->checkAndSetData($step);

			if (!($step instanceof Step))
				throw new \InvalidArgumentException(
					$step::class . ' not instance of Step. Does ' . $this->id . 'belong to a step?'
				);


			return $step;

		}

		private function getSection():Section {
			$section = $this->sectionRepository->find($this->dto->sectionId);

			if (!$section) {
				throw new NotFoundHttpException('section not found');
			}

			return $section;
		}

	}