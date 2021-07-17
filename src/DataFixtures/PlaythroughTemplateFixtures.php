<?php

namespace App\DataFixtures;

use App\Entity\Playthrough\PlaythroughTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaythroughTemplateFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($gameReference = 0; $gameReference < 10; $gameReference++) {

			$playthroughTemplatesPerGame = 2;

			for ($i = 0; $i < $playthroughTemplatesPerGame; $i++) {

				$playthroughTemplate = new PlaythroughTemplate(
					'test name' . $i, 'test description' . $i, $this->getReference(UserFixtures::USER_REFERENCE),
					$this->getReference('game_' . $gameReference), 1
				);

				$this->addReference('playthrough_template_' . ($i + ( $gameReference * $playthroughTemplatesPerGame)),
								    $playthroughTemplate);

				$manager->persist($playthroughTemplate);

			}

		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			GameFixtures::class,
			UserFixtures::class
		];
	}
}
