<?php
	namespace App\Transformer;

	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\PlaythroughTemplatePayload;
	use App\Transformer\Trait\PlaythroughTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;

	final class PlaythroughTemplateEntityTransformer extends AbstractEntityTransformer {

		use PlaythroughTrait;

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var GameEntityTransformer
		 */
		private GameEntityTransformer $gameEntityTransformer;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param GameRepository $gameRepository
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param GameEntityTransformer $gameEntityTransformer
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            GameRepository $gameRepository,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository,
		                            GameEntityTransformer $gameEntityTransformer) {

			parent::__construct($entityManager, $playthroughTemplateRepository);

			$this->gameRepository = $gameRepository;
			$this->gameEntityTransformer = $gameEntityTransformer;
		}

		/**
		 *
		 * @return PlaythroughTemplate
		 */
		public function doCreateWork(): PlaythroughTemplate {

			if (!($this->dto instanceof PlaythroughTemplatePayload)) {
				throw new InvalidPayloadException(PlaythroughTemplatePayload::class, $this->dto::class);

			}

			//Get game from database; if it is not in database, get the information from igdb and create it.
			$game = $this->getGame($this->gameEntityTransformer);

			return new PlaythroughTemplate($this->dto->name, $this->dto->description, $this->user, $game, $this->dto->visibility);

		}

		/**
		 * @return PlaythroughTemplate
		 */
		public function doUpdateWork(): PlaythroughTemplate {

			if (!($this->repository instanceof PlaythroughRepository))
				throw new InvalidRepositoryException(PlaythroughRepository::class, $this->repository::class);

			$playthroughTemplate = $this->checkAndSetData($this->repository->find($this->id));

			if (!($playthroughTemplate instanceof PlaythroughTemplate))
				throw new \InvalidArgumentException(
					$playthroughTemplate::class . ' not instance of PlaythroughTemplate. Does ' . $this->id .
					'belong to a playthrough template?'
				);

			return $playthroughTemplate;

		}

	}