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

		$this->IGDBHelper->getGameAndSave(484);
		$this->IGDBHelper->getGameAndSave(11133);
		$game = $this->IGDBHelper->getGameAndSave(11397);
		$this->addReference(self::GAME_REFERENCE, $game);

    }

	public function getDependencies(): array {
		return [
			AppFixtures::class,
		];
	}
}
