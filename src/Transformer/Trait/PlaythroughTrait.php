<?php
	namespace App\Transformer\Trait;

	use App\Entity\Game;
	use App\Entity\Playthrough\PlaythroughInterface;
	use App\Request\Payloads\GamePayload;
	use App\Request\Payloads\PlaythroughPayload;
	use App\Request\Payloads\PlaythroughTemplatePayload;
	use App\Transformer\GameEntityTransformer;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	trait PlaythroughTrait {

		/**
		 * @param PlaythroughInterface $playthrough
		 *
		 * @return PlaythroughInterface
		 * @see PlaythroughEntityTransformer
		 * @see PlaythroughTemplateEntityTransformer
		 */
		private function checkAndSetData(PlaythroughInterface $playthrough): PlaythroughInterface {

			if (!(($this->dto instanceof PlaythroughTemplatePayload) || ($this->dto instanceof PlaythroughPayload)))
				throw new \InvalidArgumentException(
					'In ' . static::class . '. Payload not instance of PlaythroughPayload or PlaythroughTemplatePayload.'
				);

			if (isset($this->dto->visibility))
				$playthrough->setVisibility($this->dto->visibility);

			if (isset($this->dto->name))
				$playthrough->setName($this->dto->name);

			if (isset($this->dto->description))
				$playthrough->setDescription($this->dto->description);

			return $playthrough;

		}

		/**
		 *
		 */
		private function getGame(GameEntityTransformer $gameEntityTransformer = null): Game {
			$game = $this->gameRepository->find($this->dto->gameId);

			//If game is not in database, let's try fetching it from igdb and creating it.
			//We only do this if gameEntityTransformer was included in the function call.
			if (!$game) {

				if ($gameEntityTransformer) {

					$gameDto = new GamePayload($this->dto->gameId);
					$game = $gameEntityTransformer->create($gameDto);

					Assert($game instanceof Game);
					return $game;

				}

				throw new NotFoundHttpException('game not found');

			}

			return $game;
		}


	}