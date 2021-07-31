<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Game\AbstractGameDTO;
	use App\DTO\Game\GameDTO;
	use App\DTO\Game\IGDBGameResponseDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Game;
	use App\Entity\User;
	use Symfony\Component\HttpFoundation\Request;

	class GameEntityTransformer extends AbstractEntityTransformer {

		private \DateTimeImmutable $releaseDateTimeImmutable;

		/**
		 * @param IGDBGameResponseDTO|GameDTO $dto
		 * @return Game
		 * @throws \Exception
		 */
		public function assemble (IGDBGameResponseDTO|GameDTO $dto): Game {

			if (!($dto->releaseDate instanceof \DateTimeImmutable)) {

				$this->releaseDateTimeImmutable = new \DateTimeImmutable(date('Y-m-d', ((int)$dto->releaseDate)));

			}

			return $this->create($dto);

		}

		/**
		 * @param DTOInterface $dto
		 * @param bool $skipValidation
		 * @return Game
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): Game {

			assert($dto instanceof AbstractGameDTO);

			if (!$skipValidation) {
				$this->validate($dto);
			}

			$game = new Game(
				$dto->genre, $dto->title, $dto->internetGameDatabaseID, $dto->screenshots, $dto->artworks, $dto->cover,
				$dto->platforms, $dto->slug, $dto->rating, $dto->summary, $dto->storyline,
				$this->releaseDateTimeImmutable ?? $dto->releaseDate
			);

			$this->entityManager->persist($game);
			$this->entityManager->flush();

			return $game;

		}

		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			// TODO: Implement update() method.
		}

		public function delete(int $id): void {
			// TODO: Implement delete() method.
		}
	}