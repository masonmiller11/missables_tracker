<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\Playthrough;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidEntityException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\PlaythroughPayload;
	use App\Transformer\Trait\PlaythroughTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	final class PlaythroughEntityTransformer extends AbstractEntityTransformer {

		use PlaythroughTrait;

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var PlaythroughTemplateRepository
		 */
		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param GameRepository $gameRepository
		 * @param PlaythroughRepository $playthroughRepository
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            GameRepository $gameRepository,
		                            PlaythroughRepository $playthroughRepository,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository) {

			parent::__construct($entityManager, $playthroughRepository);

			$this->gameRepository = $gameRepository;
			$this->playthroughTemplateRepository = $playthroughTemplateRepository;

		}

		/**
		 *
		 * @return Playthrough
		 */
		public function doCreateWork(): Playthrough {

			if (!($this->dto instanceof PlaythroughPayload))
				throw new InvalidPayloadException(PlaythroughPayload::class, $this->dto::class);

			$game = $this->getGame();

			$this->doesTemplateExist();

			return new Playthrough(
				$this->dto->name,
				$this->dto->description,
				$game,
				$this->dto->templateId,
				$this->user,
				$this->dto->visibility
			);

		}

		private function doesTemplateExist(): void {
			$template = $this->playthroughTemplateRepository->find($this->dto->templateId);

			if (!$template) {
				throw new NotFoundHttpException('Template not found');
			}
		}

		/**
		 * @return Playthrough
		 */
		public function doUpdateWork(): Playthrough {

			if (!($this->repository instanceof PlaythroughRepository))
				throw new InvalidRepositoryException(PlaythroughRepository::class, $this->repository::class);

			$playthrough = $this->checkAndSetData($this->repository->find($this->id));

			if (!($playthrough instanceof Playthrough))
				throw new InvalidEntityException(Playthrough::class, $playthrough::class);

			return $playthrough;

		}

	}