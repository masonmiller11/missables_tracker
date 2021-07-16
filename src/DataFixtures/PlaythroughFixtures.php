<?php

namespace App\DataFixtures;

use App\Entity\Playthrough\Playthrough;
use App\Repository\GameRepository;
use App\Repository\PlaythroughTemplateRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlaythroughFixtures extends Fixture
{

	private GameRepository $gameRepository;

	private UserRepository $userRepository;

	private PlaythroughTemplateRepository $playthroughTemplateRepository;

	public function __construct(GameRepository $gameRepository,
								PlaythroughTemplateRepository $playthroughTemplateRepository,
								UserRepository $userRepository) {

		$this->gameRepository = $gameRepository;
		$this->userRepository = $userRepository;
		$this->playthroughTemplateRepository = $playthroughTemplateRepository;

	}

	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {

			$playthrough = new Playthrough($this->gameRepository->find($i+1),
										   $this->playthroughTemplateRepository->find($i+1),
										   $this->userRepository->find($i+1),
										   rand(0,1));

			$manager->persist($playthrough);

		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			GameFixtures::class,
			PlaythroughTemplateFixtures::class,
			AppFixtures::class
		];
	}
}
