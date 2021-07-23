<?php

namespace App\DataFixtures;

use App\Entity\Section\SectionTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SectionTemplateFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager) {

		for ($playthroughTemplateReference = 0; $playthroughTemplateReference < 5; $playthroughTemplateReference++) {

			$sectionTemplatesPerPlaythrough = 3;

			for ($i = 0; $i < $sectionTemplatesPerPlaythrough; $i++) {

				$sectionTemplate = new SectionTemplate(
					'Test Name' . $i + 1,
					'Test Description' . $i + 1,
					$this->getReference('playthrough_template_' . $playthroughTemplateReference),
					$i + 1
				);

				$this->addReference('section_template_' . ($i + ($playthroughTemplateReference * $sectionTemplatesPerPlaythrough)),
									$sectionTemplate);

				$manager->persist($sectionTemplate);

			}

		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			PlaythroughTemplateFixtures::class,
			UserFixtures::class
		];
	}
}
