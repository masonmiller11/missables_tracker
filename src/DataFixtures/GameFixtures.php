<?php

	namespace App\DataFixtures;

	use App\Entity\Game;
	use App\Service\IGDBHelper;
	use App\Transformer\GameEntityTransformer;
	use Doctrine\Bundle\FixturesBundle\Fixture;
	use Doctrine\Common\DataFixtures\DependentFixtureInterface;
	use Doctrine\Persistence\ObjectManager;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	class GameFixtures extends Fixture implements DependentFixtureInterface {

		private const GAME_IDS = [11397, 2059, 145817, 88970, 398, 11133, 2368, 81085, 2368, 2364,
			24869, 145191, 22066, 484, 485, 480, 1209, 740, 2640, 991];

		/**
		 * @var IGDBHelper
		 */
		private IGDBHelper $IGDBHelper;

		/**
		 * @var GameEntityTransformer
		 */
		private GameEntityTransformer $entityTransformer;

		public function __construct(IGDBHelper $IGDBHelper,
		                            GameEntityTransformer $entityTransformer) {
			$this->IGDBHelper = $IGDBHelper;
			$this->entityTransformer = $entityTransformer;
		}

		/**
		 * @param ObjectManager $manager
		 * @throws TransportExceptionInterface
		 * @throws \Exception
		 */
		public function load(ObjectManager $manager) {

			$i = 0;

			foreach (self::GAME_IDS as $gameID) {

				$igdbGameDto = $this->IGDBHelper->getGameFromIGDB($gameID); //TODO we need to fix this!

				$game = $this->entityTransformer->assemble($igdbGameDto);

				$this->addReference('game_' . $i, $game);
				$manager->persist($game);
				$i++;
			}

			$manager->flush();
		}

		public function getDependencies(): array {
			return [
				AppFixtures::class,
			];
		}
	}
