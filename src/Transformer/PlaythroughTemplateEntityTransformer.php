<?php
	namespace App\Transformer;

	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\PlaythroughTemplatePayload;
	use App\Transformer\Trait\PlaythroughTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

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
		 * @param ValidatorInterface $validator
		 * @param GameRepository $gameRepository
		 * @param PlaythroughTemplateRequestDTOTransformer $DTOTransformer
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param GameEntityTransformer $gameEntityTransformer
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            GameRepository $gameRepository,
		                            PlaythroughTemplateRequestDTOTransformer $DTOTransformer,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository,
		                            GameEntityTransformer $gameEntityTransformer) {

			parent::__construct($entityManager, $validator);

			$this->gameRepository = $gameRepository;
			$this->DTOTransformer = $DTOTransformer;
			$this->repository = $playthroughTemplateRepository;
			$this->gameEntityTransformer = $gameEntityTransformer;
		}

		/**
		 *
		 * @return PlaythroughTemplate
		 */
		public function doCreateWork(): PlaythroughTemplate {

			if (!($this->dto instanceof PlaythroughTemplatePayload)) {
				throw new \InvalidArgumentException(
					'PlaythroughTemplateEntityTransformer\'s Payload not instance of PlaythroughTemplatePayload'
				);
			}

			//Get game from database; if it is not in database, get the information from igdb and create it.
			$game = $this->getGame($this->gameEntityTransformer);

			return new PlaythroughTemplate($this->dto->name, $this->dto->description, $this->user, $game, $this->dto->visibility);

		}

		/**
		 * @return PlaythroughTemplate
		 */
		public function doUpdateWork(): PlaythroughTemplate {

			$playthroughTemplate = $this->checkAndSetData($this->repository->find($this->id));

			if (!($playthroughTemplate instanceof PlaythroughTemplate))
				throw new \InvalidArgumentException(
					$playthroughTemplate::class . ' not instance of PlaythroughTemplate. Does ' . $this->id .
					'belong to a playthrough template?'
				);

			return $playthroughTemplate;

		}

	}