<?php

namespace App\DataFixtures;

use App\Entity\Section\SectionTemplate;
use App\Entity\Step\StepTemplate;
use App\Repository\PlaythroughTemplateRepository;
use App\Repository\SectionTemplateRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StepTemplateFixtures extends Fixture
{

	private SectionTemplateRepository $sectionTemplateRepository;

	public function __construct(SectionTemplateRepository $sectionTemplateRepository) {
		$this->sectionTemplateRepository = $sectionTemplateRepository;
	}

	public function load(ObjectManager $manager) {

		for ($sectionTemplateID = 0; $sectionTemplateID < 20; $sectionTemplateID ++) {
			for ($i = 0; $i < 20; $i++) {
				$section = new StepTemplate(
					'Test Name' . $i+1,
					'Test Description' . $i+1,
					$this->sectionTemplateRepository->find($sectionTemplateID+1),
					$i+1
				);

				$manager->persist($section);
			}
		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			SectionTemplateFixtures::class,
			AppFixtures::class
		];
	}
}
