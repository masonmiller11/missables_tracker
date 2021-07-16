<?php

namespace App\DataFixtures;

use App\Entity\Section\Section;
use App\Repository\PlaythroughRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SectionFixtures extends Fixture implements DependentFixtureInterface
{

	public const SECTION_REFERENCE = 'section';

	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {

			$section = new Section(
				'Test Name' . $i+1,
				'Test Description' . $i+1,
				$this->getReference(PlaythroughFixtures::PLAYTHROUGH_REFERENCE),
				$i+1
			);

			$manager->persist($section);
		}

		$manager->flush();
		$this->addReference(self::SECTION_REFERENCE, $section);

	}

	public function getDependencies(): array {
		return [
			PlaythroughFixtures::class,
			UserFixtures::class
		];
	}
}
