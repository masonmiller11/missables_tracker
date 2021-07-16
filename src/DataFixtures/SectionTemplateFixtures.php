<?php

namespace App\DataFixtures;

use App\Entity\Section\SectionTemplate;
use App\Repository\PlaythroughTemplateRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SectionTemplateFixtures extends Fixture
{

	private PlaythroughTemplateRepository $playthroughTemplateRepository;

	public function __construct(PlaythroughTemplateRepository $playthroughTemplateRepository) {
		$this->playthroughTemplateRepository = $playthroughTemplateRepository;
	}

	public function load(ObjectManager $manager) {

		for ($playthroughTemplateID = 0; $playthroughTemplateID < 20; $playthroughTemplateID ++) {
			for ($i = 0; $i < 20; $i++) {
				$section = new SectionTemplate(
					'Test Name' . $i+1,
					'Test Description' . $i+1,
					$this->playthroughTemplateRepository->find($playthroughTemplateID+1),
					$i+1
				);

				$manager->persist($section);
			}
		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			PlaythroughTemplateFixtures::class,
			AppFixtures::class
		];
	}
}
