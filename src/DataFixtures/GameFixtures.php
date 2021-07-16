<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\IGDBHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GameFixtures extends Fixture
{

	private IGDBHelper $IGDBHelper;

	public function __construct(IGDBHelper $IGDBHelper) {
		$this->IGDBHelper = $IGDBHelper;
	}

	/**
	 * @throws TransportExceptionInterface
	 */
	public function load(ObjectManager $manager) {

		for ($i = 0; $i < 20; $i++) {

			$this->IGDBHelper->getGameAndSave(146173 + rand(0,100));
		}

    }

	public function getDependencies(): array {
		return [
			AppFixtures::class,
		];
	}
}
