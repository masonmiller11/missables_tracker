<?php

namespace App\DataFixtures;

use App\Entity\Playthrough\PlaythroughTemplate;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlaythroughTemplateFixtures extends Fixture
{

	private GameRepository $gameRepository;

	private UserRepository $userRepository;

	public function __construct(GameRepository $gameRepository, UserRepository $userRepository) {
		$this->gameRepository = $gameRepository;
		$this->userRepository = $userRepository;
	}

	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {

			$playthroughTemplate = new PlaythroughTemplate($this->userRepository->find($i+1),
				$this->gameRepository->find($i+1),rand(0,1));

			$manager->persist($playthroughTemplate);

		}

		$manager->flush();

    }

	public function getDependencies(): array {
		return [
			GameFixtures::class,
			AppFixtures::class
		];
	}
}
