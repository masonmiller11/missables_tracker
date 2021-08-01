<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Section\SectionDTO;
	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\Section;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\User;
	use App\Repository\AbstractBaseRepository;
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

	final class SectionEntityTransformer extends AbstractSectionEntityTransformer {

		/**
		 * @var PlaythroughRepository
		 */
		private PlaythroughRepository $playthroughRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param SectionRequestTransformer $DTOTransformer
		 * @param PlaythroughRepository $playthroughRepository
		 * @param SectionRepository $sectionRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
									SectionRequestTransformer $DTOTransformer,
		                            PlaythroughRepository $playthroughRepository,
									SectionRepository $sectionRepository) {

			parent::__construct($entityManager, $validator);

			$this->DTOTransformer = $DTOTransformer;
			$this->repository = $sectionRepository;
			$this->playthroughRepository = $playthroughRepository;

		}

		/**
		 * @param SectionDTO $dto
		 * @param User $user
		 *
		 * @return Section
		 */
		public function assemble (SectionDTO $dto, User $user): Section {

			$this->user = $user;

			return $this->create($dto);

		}

		/**
		 *
		 * @param DTOInterface $dto
		 * @param bool         $skipValidation
		 *
		 * @return Section
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): Section {

			if (!$skipValidation) {
				$this->validate($dto);
			}

			assert($dto instanceof SectionDTO);

			$playthrough = $this->playthroughRepository->find($dto->playthroughId);

			if (!$playthrough) {
				throw new NotFoundHttpException('playthrough not found');
			}

			$section = new Section($dto->name, $dto->description, $playthrough, $dto->position);

			$this->entityManager->persist($section);
			$this->entityManager->flush();

			return $section;

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return EntityInterface
		 */
		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);

			$section = $this->repository->find($id);

			$tempDTO->playthroughId = $section->getPlaythrough()->getId();

			//TODO Can we $this->>DTOTransformer in parent abstract class
			//TODO... and set $this->DTOTransformer in this class?

			$this->validate($tempDTO);

			return $this->doUpdate(json_decode($request->getContent(), true), $section);

		}

	}