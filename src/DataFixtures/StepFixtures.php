<?php

namespace App\DataFixtures;

use App\Entity\Step\Step;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StepFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($sectionReference = 0; $sectionReference < 15; $sectionReference++) {

			$stepsPerSection = 4;

			for ($i = 0; $i < $stepsPerSection; $i++) {

				$step = new Step(
					'Test Name' . $i + 1,
					'Test Description' . $i + 1,
					$this->getReference('section_' . $sectionReference),
					$i + 1 + ($sectionReference * $stepsPerSection)
				);

				$manager->persist($step);
			}

		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			SectionFixtures::class,
			UserFixtures::class
		];
	}
}
