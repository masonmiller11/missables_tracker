<?php
	namespace App\Transformer;

	use App\DTO\Game\IGDBGameResponseDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Game;
	use App\Exception\DuplicateResourceException;
	use App\Exception\InvalidPayloadException;
	use App\Exception\InvalidRepositoryException;
	use App\Repository\GameRepository;
	use App\Request\Payloads\GamePayload;
	use App\Service\IGDBHelper;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\NonUniqueResultException;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	final class GameEntityTransformer extends AbstractEntityTransformer {

		private IGDBHelper $IGDBHelper;

		/**
		 * GameEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param GameRepository $repository
		 * @param IGDBHelper $IGDBHelper
		 */
		#[Pure] public function __construct(EntityManagerInterface $entityManager,
		                                    GameRepository $repository,
		                                    IGDBHelper $IGDBHelper) {

			parent::__construct($entityManager, $repository);

			$this->IGDBHelper = $IGDBHelper;

		}

		/**
		 * @return Game
		 * @throws \Exception
		 * @throws TransportExceptionInterface
		 */
		protected function doCreateWork(): Game {

			if (!($this->dto instanceof GamePayload))
				throw new InvalidPayloadException(GamePayload::class, $this->dto::class);

			if (!($this->checkIfGameIsAdded()))
				throw new DuplicateResourceException('A game with this IGDB id has already been added');

			//This method builds, validates, and then returns an IGDBGameResponseDTO
			$igdbGameDto = $this->IGDBHelper->getGameFromIGDB($this->dto->internetGameDatabaseID);

			return $this->assemble($igdbGameDto);

		}

		/**
		 * @throws \Exception
		 */
		public function createManyFromIgdbData (array $gameDtos): array {

			$games = [];

			foreach ($gameDtos as $dto) {
				$games[] = $this->assemble($dto);
			}

			foreach ($games as $game) {

				if (!($this->checkIfGameIsAdded($game->getInternetGameDatabaseID())))
					$this->entityManager->persist($game);

			}

			$this->entityManager->flush();

			return $games;

	}

		/**
		 * @throws NonUniqueResultException
		 */
		private function checkIfGameIsAdded(int $id = null): bool {

			return (bool)$this->getIGDBGameIfInDatabase($id ?? $this->dto->internetGameDatabaseID);

		}

		/**
		 * @throws NonUniqueResultException
		 */
		private function getIGDBGameIfInDatabase(int $id): Game|NonUniqueResultException|null {

			if (!($this->repository instanceof GameRepository))
				throw new InvalidRepositoryException(GameRepository::class, $this->repository::class);

			return $this->repository->findGameByInternetGameDatabaseID($id);
		}

		/**
		 * @param IGDBGameResponseDTO $igdbGameDto
		 * @return Game
		 * @throws \Exception
		 */
		#[Pure] public function assemble(IGDBGameResponseDTO $igdbGameDto): Game {

			return new Game(
				$igdbGameDto->genres,
				$igdbGameDto->title,
				$igdbGameDto->internetGameDatabaseID,
				$igdbGameDto->screenshots,
				$igdbGameDto->artworks,
				$igdbGameDto->cover,
				$igdbGameDto->platforms,
				$igdbGameDto->slug,
				$igdbGameDto->rating,
				$igdbGameDto->summary,
				$igdbGameDto->storyline,
				$igdbGameDto->releaseDate
			);

		}

		protected function doUpdateWork(): EntityInterface {
			// no op
		}

	}