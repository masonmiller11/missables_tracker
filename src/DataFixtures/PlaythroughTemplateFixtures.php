<?php

namespace App\DataFixtures;

use App\Entity\Playthrough\PlaythroughTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaythroughTemplateFixtures extends Fixture implements DependentFixtureInterface
{
	public const PLAYTHROUGH_TEMPLATE_REFERENCE = 'playthrough template';

	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {

			$playthroughTemplate = new PlaythroughTemplate('test name' . $i, 'test description'. $i, $this->getReference(UserFixtures::USER_REFERENCE),
				$this->getReference(GameFixtures::GAME_REFERENCE),1);

			$manager->persist($playthroughTemplate);

		}

		$manager->flush();
		$this->addReference(self::PLAYTHROUGH_TEMPLATE_REFERENCE, $playthroughTemplate);

    }

	public function getDependencies(): array {
		return [
			GameFixtures::class,
			UserFixtures::class
		];
	}
}
