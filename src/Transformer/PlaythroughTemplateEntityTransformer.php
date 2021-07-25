<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\User;
	use App\Repository\GameRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class PlaythroughTemplateEntityTransformer extends AbstractEntityTransformer {

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		private User $user;

		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            GameRepository $gameRepository) {

			parent::__construct($entityManager, $validator);

			$this->gameRepository = $gameRepository;

		}

		/**
		 * @param PlaythroughTemplateDTO $dto
		 * @param User $user
		 *
		 * @return PlaythroughTemplate
		 */
		public function assemble (PlaythroughTemplateDTO $dto, User $user): PlaythroughTemplate {

			$this->user = $user;

			return $this->create($dto);

		}

		/**
		 *
		 * @param DTOInterface $dto
		 * @param bool $skipValidation
		 *
		 * @return PlaythroughTemplate
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): PlaythroughTemplate {

			if (!$skipValidation) {
				$this->validate($dto);
			}

			assert($dto instanceof PlaythroughTemplateDTO);

			$game = $this->gameRepository->find($dto->gameID);

			$playthroughTemplate = new PlaythroughTemplate($dto->name, $dto->description, $this->user, $game, $dto->visibility);

			$this->entityManager->persist($playthroughTemplate);
			$this->entityManager->flush();

			return $playthroughTemplate;

		}

		public function update(DTOInterface $dto, bool $skipValidation = false): EntityInterface {
			// TODO: Implement update() method.
		}

		public function delete(DTOInterface $dto, bool $skipValidation = false): EntityInterface {
			// TODO: Implement delete() method.
		}
	}