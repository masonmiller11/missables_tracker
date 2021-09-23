<?php
	namespace App\Transformer;

	use App\DTO\Game\GameDTO;
	use App\DTO\Game\IGDBGameResponseDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Game;
	use App\Exception\DuplicateResourceException;
	use App\Repository\GameRepository;
	use App\Request\Payloads\GamePayload;
	use App\Service\IGDBHelper;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\NonUniqueResultException;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Validator\Validator\ValidatorInterface;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	final class GameEntityTransformer extends AbstractEntityTransformer {

		private IGDBHelper $IGDBHelper;

		/**
		 * GameEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param GameRepository $repository
		 * @param IGDBHelper $IGDBHelper
		 */
		#[Pure] public function __construct(EntityManagerInterface $entityManager,
		                                    ValidatorInterface $validator,
		                                    GameRepository $repository,
		                                    IGDBHelper $IGDBHelper) {

			parent::__construct($entityManager, $validator);
			$this->repository = $repository;
			$this->IGDBHelper = $IGDBHelper;

		}

		/**
		 * @param IGDBGameResponseDTO $igdbGameDto
		 * @return Game
		 * @throws \Exception
		 */
		#[Pure] public function assemble(IGDBGameResponseDTO $igdbGameDto): Game {

			return new Game(
				$igdbGameDto->genres, $igdbGameDto->title, $igdbGameDto->internetGameDatabaseID,
				$igdbGameDto->screenshots, $igdbGameDto->artworks, $igdbGameDto->cover,
				$igdbGameDto->platforms, $igdbGameDto->slug, $igdbGameDto->rating, $igdbGameDto->summary,
				$igdbGameDto->storyline, $igdbGameDto->releaseDate
			);

		}

		/**
		 * @return Game
		 * @throws \Exception
		 * @throws TransportExceptionInterface
		 */
		protected function doCreateWork(): Game {

			if (!($this->dto instanceof GamePayload)) {
				throw new \InvalidArgumentException('GameEntityTransformer\'s DTO not instance of AbstractGameDTO');
			}

			$this->checkIfGameIsAdded();

			//This method builds, validates, and then returns an IGDBGameResponseDTO
			$igdbGameDto = $this->IGDBHelper->getGameFromIGDB($this->dto->internetGameDatabaseID);

			return $this->assemble($igdbGameDto);

		}

		/**
		 * @throws NonUniqueResultException
		 */
		private function getIGDBGameIfInDatabase(int $id): Game|NonUniqueResultException|null {
			return $this->repository->findGameByInternetGameDatabaseID($id);
		}

		/**
		 * @throws DuplicateResourceException
		 * @throws NonUniqueResultException
		 */
		private function checkIfGameIsAdded(): void {
			if ($this->getIGDBGameIfInDatabase($this->dto->internetGameDatabaseID))
				throw new DuplicateResourceException('A game with this IGDB id has already been added');

		}

		protected function doUpdateWork(int $id, Request $request, bool $skipValidation): EntityInterface {
			// no op
		}
	}