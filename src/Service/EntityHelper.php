<?php
	namespace App\Service;

	use App\DTO\DTOInterface;
	use App\Exception\ValidationException;
	use App\DTO\GameDTO;
	use App\Entity\Game;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class EntityHelper {

		private ValidatorInterface $validator;

		private EntityManagerInterface $entityManager;

		public function __construct(ValidatorInterface $validator,
									EntityManagerInterface $entityManager) {

			$this->validator = $validator;
			$this->entityManager = $entityManager;

		}

		/**
		 * @param GameDTO $dto
		 *
		 * @return Game*
		 * @throws \Exception
		 */
		public function createGame (DTOInterface $dto): Game {

			$releaseDateTimeImmutable = new \DateTimeImmutable(date('Y-m-d', ((int)$dto->releaseDate)));

			$game = new Game(
				$dto->genre, $dto->title, $dto->internetGameDatabaseID, $dto->screenshots, $dto->artworks, $dto->cover,
				$dto->platforms, $dto->slug, $dto->rating, $dto->summary, $dto->storyline, $releaseDateTimeImmutable
			);

			$this->entityManager->persist($game);
			$this->entityManager->flush();

			return $game;

		}

	}