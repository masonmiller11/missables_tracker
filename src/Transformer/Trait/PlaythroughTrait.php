<?php
	namespace App\Transformer\Trait;

	use App\Entity\Game;
	use App\Entity\Playthrough\PlaythroughInterface;
	use App\Request\Payloads\GamePayload;
	use App\Transformer\GameEntityTransformer;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	trait PlaythroughTrait {

		/**
		 * @see PlaythroughTemplateEntityTransformer
		 * @see PlaythroughEntityTransformer
		 * @param array                $data
		 * @param PlaythroughInterface $playthrough
		 *
		 * @return PlaythroughInterface
		 */
		private function checkAndSetData (array $data, PlaythroughInterface $playthrough): PlaythroughInterface {

			if (isset($data['visibility'])) {
				$playthrough->setVisibility($data['visibility']);
			}
			if (isset($data['name'])) {
				$playthrough->setName($data['name']);
			}
			if (isset($data['description'])) {
				$playthrough->setDescription($data['description']);
			}

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

					Assert ($game instanceof Game);
					return $game;

				}

				throw new NotFoundHttpException('game not found');

			}

			return $game;
		}



	}