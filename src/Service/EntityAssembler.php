<?php
	namespace App\Service;

	use App\DTO\DTOInterface;
	use App\DTO\GameDTO;
	use App\Entity\Game;

	class EntityAssembler {

		/**
		 * @param GameDTO $dto
		 *
		 * @return Game*
		 * @throws \Exception
		 */
		public function createGame (DTOInterface $dto): Game {

			if (!($dto->releaseDate instanceof \DateTimeImmutable)) {

				$releaseDateTimeImmutable = new \DateTimeImmutable(date('Y-m-d', ((int)$dto->releaseDate)));

			}

			return new Game(
				$dto->genre, $dto->title, $dto->internetGameDatabaseID, $dto->screenshots, $dto->artworks, $dto->cover,
				$dto->platforms, $dto->slug, $dto->rating, $dto->summary, $dto->storyline,
				$releaseDateTimeImmutable ?? $dto->releaseDate
			);

		}

	}