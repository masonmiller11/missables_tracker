<?php
	namespace App\Transformer;

	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Section\Section;
	use App\Exception\ValidationException;
	use App\Repository\PlaythroughRepository;
	use App\Repository\SectionRepository;
	use App\Request\Payloads\SectionPayload;
	use App\Transformer\Trait\StepSectionTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class SectionEntityTransformer extends AbstractEntityTransformer {

		use StepSectionTrait;

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
		public function doCreateWork(): Section {

			if (!($this->dto instanceof SectionPayload)) {
				throw new \InvalidArgumentException('SectionEntityTransformer\'s DTO not instance of SectionDTO');
			}

			$playthrough = $this->getPlaythrough();

			return new Section($this->dto->name, $this->dto->name, $playthrough, $this->dto->position);

		}

		/**
		 * @return Playthrough
		 */
		private function getPlaythrough(): Playthrough {
			$playthrough = $this->playthroughRepository->find($this->dto->playthroughId);

			if (!$playthrough) {
				throw new NotFoundHttpException('playthrough not found');
			}

			return $playthrough;
		}

		/**
		 * @return Section
		 */
		public function doUpdateWork(): Section {

			$section = $this->repository->find($this->id);

			$section = $this->checkAndSetData($section));

			if (!($section instanceof Section))
				throw new \InvalidArgumentException(
					$section::class . ' not instance of Playthrough. Does ' . $this->id . 'belong to a section?'
				);

			return $section;

		}

	}