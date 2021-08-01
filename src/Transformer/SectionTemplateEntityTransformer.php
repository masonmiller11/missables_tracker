<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionTemplateRequestTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\User;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionTemplateRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class SectionTemplateEntityTransformer extends AbstractSectionEntityTransformer {

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var User
		 */
		private User $user;

		/**
		 * @var SectionTemplateRequestTransformer
		 */
		private SectionTemplateRequestTransformer $DTOTransformer;

		/**
		 * @var PlaythroughTemplateRepository
		 */
		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		private SectionTemplateRepository $sectionTemplateRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface            $entityManager
		 * @param ValidatorInterface                $validator
		 * @param GameRepository                    $gameRepository
		 * @param SectionTemplateRequestTransformer $DTOTransformer
		 * @param PlaythroughTemplateRepository     $playthroughTemplateRepository
		 * @param SectionTemplateRepository         $sectionTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            GameRepository $gameRepository,
									SectionTemplateRequestTransformer $DTOTransformer,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository,
									SectionTemplateRepository $sectionTemplateRepository) {

			parent::__construct($entityManager, $validator);

			$this->gameRepository = $gameRepository;
			$this->DTOTransformer = $DTOTransformer;
			$this->sectionTemplateRepository = $sectionTemplateRepository;
			$this->playthroughTemplateRepository = $playthroughTemplateRepository;

		}

		/**
		 * @param SectionTemplateDTO $dto
		 * @param User               $user
		 *
		 * @return SectionTemplate
		 */
		public function assemble (SectionTemplateDTO $dto, User $user): SectionTemplate {

			$this->user = $user;

			return $this->create($dto);

		}

		/**
		 *
		 * @param DTOInterface $dto
		 * @param bool         $skipValidation
		 *
		 * @return SectionTemplate
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): SectionTemplate {

			if (!$skipValidation) {
				$this->validate($dto);
			}

			assert($dto instanceof SectionTemplateDTO);

			$playthroughTemplate = $this->playthroughTemplateRepository->find($dto->templateId);

			if (!$playthroughTemplate) {
				throw new NotFoundHttpException('template not found');
			}

			$sectionTemplate = new SectionTemplate($dto->name, $dto->description, $playthroughTemplate, $dto->position);

			$this->entityManager->persist($sectionTemplate);
			$this->entityManager->flush();

			return $sectionTemplate;

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return EntityInterface
		 */
		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);

			$sectionTemplate = $this->sectionTemplateRepository->find($id);

			$tempDTO->templateId = $sectionTemplate->getPlaythrough()->getId();

			$this->validate($tempDTO);

			return $this->doUpdate(json_decode($request->getContent(), true), $sectionTemplate);

		}

		/**
		 * @param int $id
		 */
		public function delete(int $id): void {

			$sectionTemplate = $this->sectionTemplateRepository->find($id);

			$this->entityManager->remove($sectionTemplate);
			$this->entityManager->flush();

			//TODO move this functionality into a doDelete method on AbstractEntityTransformer
			//TODO ... that takes in the repository and entity as parameters.

		}
	}