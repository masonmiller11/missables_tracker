<?php

namespace App\DataFixtures;

use App\Entity\Step\Step;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StepFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {
			$step = new Step(
				'Test Name' . $i+1,
				'Test Description' . $i+1,
				$this->getReference(SectionFixtures::SECTION_REFERENCE),
				$i+1
			);

			$manager->persist($step);
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
