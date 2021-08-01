<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Section\SectionDTO;
	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Step\StepDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\DTO\Transformer\RequestTransformer\Step\StepRequestTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\Section;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\Step\Step;
	use App\Entity\User;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Repository\SectionTemplateRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class StepEntityTransformer extends AbstractStepEntityTransformer {

		/**
		 * @var User
		 */
		private User $user;

		/**
		 * @var StepRequestTransformer
		 */
		private StepRequestTransformer $DTOTransformer;

		/**
		 * @var SectionRepository
		 */
		private SectionRepository $sectionRepository;

		private StepRepository $stepRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface     $validator
		 * @param GameRepository         $gameRepository
		 * @param StepRequestTransformer $DTOTransformer
		 * @param SectionRepository      $sectionRepository
		 * @param StepRepository         $stepRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            GameRepository $gameRepository,
									StepRequestTransformer $DTOTransformer,
		                            SectionRepository $sectionRepository,
									StepRepository $stepRepository) {

			parent::__construct($entityManager, $validator);

			$this->DTOTransformer = $DTOTransformer;
			$this->stepRepository = $stepRepository;
			$this->sectionRepository = $sectionRepository;

		}

		/**
		 * @param StepDTO $dto
		 * @param User $user
		 *
		 * @return Step
		 */
		public function assemble (StepDTO $dto, User $user): Step {

			$this->user = $user;

			return $this->create($dto);

		}

		/**
		 *
		 * @param DTOInterface $dto
		 * @param bool         $skipValidation
		 *
		 * @return Step
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): Step {

			if (!$skipValidation) {
				$this->validate($dto);
			}

			assert($dto instanceof StepDTO);

			$section = $this->sectionRepository->find($dto->sectionId);

			if (!$section) {
				throw new NotFoundHttpException('section not found');
			}

			$step = new Step($dto->name, $dto->description, $section, $dto->position);

			$this->entityManager->persist($section);
			$this->entityManager->flush();

			return $step;

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return EntityInterface
		 */
		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);

			$step = $this->stepRepository->find($id);

			$tempDTO->sectionId = $step->getSection()->getId();

			//TODO Can we $this->>DTOTransformer in parent abstract class
			//TODO... and set $this->DTOTransformer in this class?

			$this->validate($tempDTO);

			return $this->doUpdate(json_decode($request->getContent(), true), $step);

		}

		/**
		 * @param int $id
		 */
		public function delete(int $id): void {

			$step = $this->stepRepository->find($id);

			$this->entityManager->remove($step);
			$this->entityManager->flush();

			//TODO move this functionality into a doDelete method on AbstractEntityTransformer
			//TODO ... that takes in the repository and entity as parameters.

		}
	}