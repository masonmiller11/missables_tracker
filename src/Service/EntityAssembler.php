<?php
	namespace App\Service;

	use App\DTO\Game\GameDTO;
	use App\DTO\Game\IGDBGameResponseDTO;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\Entity\Game;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\User;
	use App\Repository\GameRepository;
	use App\Repository\UserRepository;

	class EntityAssembler {

		/**
		 * @param GameDTO|IGDBGameResponseDTO $dto
		 *
		 * @return Game*
		 * @throws \Exception
		 */
		public static function assembleGame (IGDBGameResponseDTO|GameDTO $dto): Game {

			if (!($dto->releaseDate instanceof \DateTimeImmutable)) {

				$releaseDateTimeImmutable = new \DateTimeImmutable(date('Y-m-d', ((int)$dto->releaseDate)));

			}

			return new Game(
				$dto->genre, $dto->title, $dto->internetGameDatabaseID, $dto->screenshots, $dto->artworks, $dto->cover,
				$dto->platforms, $dto->slug, $dto->rating, $dto->summary, $dto->storyline,
				$releaseDateTimeImmutable ?? $dto->releaseDate
			);

		}

		public static function assembePlaythroughTemplate (PlaythroughTemplateDTO $dto,
													Game $game,
													User $user): PlaythroughTemplate {

			return new PlaythroughTemplate($dto->name, $dto->description, $user, $game, $dto->visibility);

		}

	}