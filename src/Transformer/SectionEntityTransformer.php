<?php
	namespace App\Transformer;

	use App\DTO\Section\SectionDTO;
	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\Entity\Section\Section;
	use App\Repository\PlaythroughRepository;
	use App\Repository\SectionRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class SectionEntityTransformer extends AbstractEntityTransformer {

		use StepSectionCheckDataTrait;

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
		 *
		 * @return Section
		 */
		public function doCreateWork (): Section {

			assert($this->dto instanceof SectionDTO);

			$playthrough = $this->playthroughRepository->find($this->dto->playthroughId);

			if (!$playthrough) {
				throw new NotFoundHttpException('playthrough not found');
			}

			return new Section($this->dto->name, $this->dto->name, $playthrough, $this->dto->name);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return Section
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): Section {

			$section = $this->repository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->playthroughId = $section->getPlaythrough()->getId();
			$this->validate($tempDTO);

			$section = $this->checkData($section, json_decode($request->getContent(), true));

			Assert ($section instanceof Section);

			return $section;

		}

	}