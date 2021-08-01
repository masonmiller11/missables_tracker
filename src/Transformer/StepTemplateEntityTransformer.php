<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Section\SectionDTO;
	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Step\StepDTO;
	use App\DTO\Step\StepTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\DTO\Transformer\RequestTransformer\Step\StepRequestTransformer;
	use App\DTO\Transformer\RequestTransformer\Step\StepTemplateRequestTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\Section;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\Step\Step;
	use App\Entity\Step\StepTemplate;
	use App\Entity\User;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Repository\SectionTemplateRepository;
	use App\Repository\StepTemplateRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class StepTemplateEntityTransformer extends AbstractStepEntityTransformer {

		/**
		 * @var User
		 */
		private User $user;

		/**
		 * @var StepTemplateRequestTransformer
		 */
		private StepTemplateRequestTransformer $DTOTransformer;

		/**
		 * @var SectionTemplateRepository
		 */
		private SectionTemplateRepository $sectionTemplateRepository;

		private StepTemplateRepository $stepTemplateRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface         $entityManager
		 * @param ValidatorInterface             $validator
		 * @param GameRepository                 $gameRepository
		 * @param StepTemplateRequestTransformer $DTOTransformer
		 * @param SectionTemplateRepository      $sectionRepository
		 * @param StepTemplateRepository         $stepTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            GameRepository $gameRepository,
									StepTemplateRequestTransformer $DTOTransformer,
		                            SectionTemplateRepository $sectionRepository,
									StepTemplateRepository $stepTemplateRepository) {

			parent::__construct($entityManager, $validator);

			$this->DTOTransformer = $DTOTransformer;
			$this->stepTemplateRepository = $stepTemplateRepository;
			$this->sectionTemplateRepository = $sectionRepository;

		}

		/**
		 * @param StepTemplateDTO $dto
		 * @param User $user
		 *
		 * @return StepTemplate
		 */
		public function assemble (StepTemplateDTO $dto, User $user): StepTemplate {

			$this->user = $user;

			return $this->create($dto);

		}

		/**
		 *
		 * @param DTOInterface $dto
		 * @param bool         $skipValidation
		 *
		 * @return StepTemplate
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): StepTemplate {

			if (!$skipValidation) {
				$this->validate($dto);
			}

			assert($dto instanceof StepTemplateDTO);

			$sectionTemplate = $this->sectionTemplateRepository->find($dto->sectionTemplateId);

			if (!$sectionTemplate) {
				throw new NotFoundHttpException('section template not found');
			}

			$stepTemplate = new StepTemplate($dto->name, $dto->description, $sectionTemplate, $dto->position);

			$this->entityManager->persist($stepTemplate);
			$this->entityManager->flush();

			return $stepTemplate;

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return EntityInterface
		 */
		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);

			$stepTemplate = $this->stepTemplateRepository->find($id);

			$tempDTO->sectionTemplateId = $stepTemplate->getSection()->getId();

			//TODO Can we $this->>DTOTransformer in parent abstract class
			//TODO... and set $this->DTOTransformer in this class?

			$this->validate($tempDTO);

			return $this->doUpdate(json_decode($request->getContent(), true), $stepTemplate);

		}

		/**
		 * @param int $id
		 */
		public function delete(int $id): void {

			$stepTemplate = $this->stepTemplateRepository->find($id);

			$this->entityManager->remove($stepTemplate);
			$this->entityManager->flush();

			//TODO move this functionality into a doDelete method on AbstractEntityTransformer
			//TODO ... that takes in the repository and entity as parameters.

		}
	}