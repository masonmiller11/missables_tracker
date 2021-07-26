<?php

namespace App\DataFixtures;

use App\Entity\Playthrough\Playthrough;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaythroughFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($gameReference = 0; $gameReference < 2; $gameReference++) {

			$playthroughTemplatesPerGame = 2;

			for ($playthroughTemplateReference = 0; $playthroughTemplateReference < $playthroughTemplatesPerGame; $playthroughTemplateReference++) {

				$playthroughsPerTemplate = 5;

				for ($i = 0; $i < $playthroughsPerTemplate; $i++) {

					$playthrough = new Playthrough(
						'test name' . $i,
						'test description' . $i,
						$this->getReference('game_' . $gameReference),
						1+$i,
						$this->getReference(UserFixtures::USER_REFERENCE),
						rand(0, 1)
					);

					$this->addReference('playthrough_' .
						($i + (
								($gameReference * ($playthroughTemplatesPerGame * $playthroughsPerTemplate)) +
								($playthroughTemplateReference * $playthroughsPerTemplate)))
						, $playthrough);
					$manager->persist($playthrough);
				}

			}

		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			GameFixtures::class,
			PlaythroughTemplateFixtures::class,
			UserFixtures::class
		];
	}
}
