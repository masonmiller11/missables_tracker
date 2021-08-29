<?php
	namespace App\Transformer;

	use App\DTO\Game\AbstractGameDTO;
	use App\DTO\Game\GameDTO;
	use App\DTO\Game\IGDBGameResponseDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Game;
	use Symfony\Component\HttpFoundation\Request;

	final class GameEntityTransformer extends AbstractEntityTransformer {

		private \DateTimeImmutable $releaseDateTimeImmutable;

		/**
		 * @param IGDBGameResponseDTO|GameDTO $dto
		 * @return Game
		 * @throws \Exception
		 */
		public function assemble (IGDBGameResponseDTO|GameDTO $dto): Game {

			$this->dto = $dto;

			$game = $this->doCreateWork();

			$this->entityManager->persist($game);
			$this->entityManager->flush();

			return $game;

		}

		/**
		 * @return Game
		 * @throws \Exception
		 */
		protected function doCreateWork(): Game {

			if (!($this->dto instanceof AbstractGameDTO)) {

			if (!($this->dto->releaseDate instanceof \DateTimeImmutable)) {

				$this->releaseDateTimeImmutable = new \DateTimeImmutable(date('Y-m-d', ((int)$this->dto->releaseDate)));

			}

			return new Game(
				$this->dto->genre, $this->dto->title, $this->dto->internetGameDatabaseID, $this->dto->screenshots, $this->dto->artworks, $this->dto->cover,
				$this->dto->platforms, $this->dto->slug, $this->dto->rating, $this->dto->summary, $this->dto->storyline,
				$this->releaseDateTimeImmutable ?? $this->dto->releaseDate
			);

		}

		protected function doUpdateWork(int $id, Request $request, bool $skipValidation): EntityInterface {
			// no op
		}
	}