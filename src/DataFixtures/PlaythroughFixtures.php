<?php

namespace App\DataFixtures;

use App\Entity\Playthrough\Playthrough;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaythroughFixtures extends Fixture implements DependentFixtureInterface
{

	public const PLAYTHROUGH_REFERENCE = 'playthrough';

	public function load(ObjectManager $manager) {

		for ($g = 1; $g <20; $g++) {

			for ($i = 0; $i < 20; $i++) {

				$playthrough = new Playthrough(
					'test name' . $i, 'test description' . $i, $this->getReference('game_' . $g),
					$this->getReference(PlaythroughTemplateFixtures::PLAYTHROUGH_TEMPLATE_REFERENCE),
					$this->getReference(UserFixtures::USER_REFERENCE),
					rand(0, 1)
				);

				$manager->persist($playthrough);

			}

		}

		$this->addReference(self::PLAYTHROUGH_REFERENCE, $playthrough);

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
