<?php

namespace App\DataFixtures;

use App\Entity\Section\Section;
use App\Repository\PlaythroughRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SectionFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($playthroughReference = 0; $playthroughReference < 5; $playthroughReference++) {

			$sectionsPerPlaythrough = 3;

			for ($i = 0; $i < $sectionsPerPlaythrough; $i++) {

				$section = new Section(
					'Test Name' . $i + 1,
					'Test Description' . $i + 1,
					$this->getReference('playthrough_' . $playthroughReference),
					$i + 1
				);

				$this->addReference('section_' . ($i + ($playthroughReference * $sectionsPerPlaythrough)), $section);
				$manager->persist($section);

			}

		}

		$manager->flush();

	}

	public function getDependencies(): array {
		return [
			PlaythroughFixtures::class,
			UserFixtures::class
		];
	}
}
