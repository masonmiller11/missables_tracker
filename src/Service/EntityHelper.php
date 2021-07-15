<?php
	namespace App\Service;

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
		 * @return Game
		 * @throws \Exception
		 *
		 * Validates DTO, creates Game entity based off DTO and then returns new Game entity.
		 */
		public function createGame (GameDTO $dto): Game {

			$errors = $this->validator->validate($dto);

			if (count($errors) > 0) {
				$errorString = (string)$errors;
				throw new ValidationException($errorString);
			}

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