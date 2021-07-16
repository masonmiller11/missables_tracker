<?php

namespace App\DataFixtures;

use App\Service\IGDBHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GameFixtures extends Fixture implements DependentFixtureInterface
{

	private IGDBHelper $IGDBHelper;

	public function __construct(IGDBHelper $IGDBHelper) {
		$this->IGDBHelper = $IGDBHelper;
	}

	public const GAME_REFERENCE = 'game';

	/**
	 * @throws TransportExceptionInterface
	 */
	public function load(ObjectManager $manager) {

		$game = $this->IGDBHelper->getGameAndSave(11397);
		$this->addReference(self::GAME_REFERENCE, $game);

//		$IGBDgameIDs = [11397,2059,145817,88970,398,11133,2368,81085,2368,2364,
//			24869,145191,22066,484,485,480,1209,740,2640,991];
//
//		foreach ($IGBDgameIDs as $gameID) {
//			$game = $this->IGDBHelper->getGameAndSave($gameID);
//			$this->addReference(self::GAME_REFERENCE, $game);
//		}

    }

	public function getDependencies(): array {
		return [
			AppFixtures::class,
		];
	}
}
