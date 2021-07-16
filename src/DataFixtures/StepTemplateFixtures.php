<?php

namespace App\DataFixtures;

use App\Entity\Step\StepTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StepTemplateFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {

			$stepTemplate = new StepTemplate(
				'Test Name' . $i+1,
				'Test Description' . $i+1,
				$this->getReference(SectionTemplateFixtures::SECTION_TEMPLATE_REFERENCE),
				$i+1
			);

			$manager->persist($stepTemplate);
		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			SectionTemplateFixtures::class,
			UserFixtures::class
		];
	}
}
