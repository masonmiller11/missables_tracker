<?php
	namespace App\Service;
	
	use App\DTO\Game\GameDTO;
	use App\DTO\Game\IGDBGameResponseDTO;
	use App\Entity\Game;

	class EntityAssembler {

		/**
		 * @param GameDTO|IGDBGameResponseDTO $dto
		 *
		 * @return Game*
		 * @throws \Exception
		 */
		public function assembleGame (IGDBGameResponseDTO|GameDTO $dto): Game {

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