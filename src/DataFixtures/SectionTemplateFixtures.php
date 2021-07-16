<?php

namespace App\DataFixtures;

use App\Entity\Section\SectionTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SectionTemplateFixtures extends Fixture implements DependentFixtureInterface
{

	public const SECTION_TEMPLATE_REFERENCE = 'section template';

	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {
			$sectionTemplate = new SectionTemplate(
				'Test Name' . $i+1,
				'Test Description' . $i+1,
				$this->getReference(PlaythroughTemplateFixtures::PLAYTHROUGH_TEMPLATE_REFERENCE),
				$i+1
			);

			$manager->persist($sectionTemplate);
		}

		$manager->flush();
		$this->addReference(self::SECTION_TEMPLATE_REFERENCE, $sectionTemplate);

    }

	public function getDependencies(): array {
		return [
			PlaythroughTemplateFixtures::class,
			UserFixtures::class
		];
	}
}
