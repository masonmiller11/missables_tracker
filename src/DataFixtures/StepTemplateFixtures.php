<?php

namespace App\DataFixtures;

use App\Entity\Step\StepTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StepTemplateFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($sectionTemplateReference = 0; $sectionTemplateReference < 15; $sectionTemplateReference++) {

			$stepsPerSection = 4;

			for ($i = 0; $i < $stepsPerSection; $i++) {

				$stepTemplate = new StepTemplate(
					'Test Name' . $i + 1,
					'Test Description' . $i + 1,
					$this->getReference('section_template_' . $sectionTemplateReference),
					$i + 1 + ($sectionTemplateReference * $stepsPerSection)
				);

				$manager->persist($stepTemplate);

			}

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
