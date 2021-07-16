<?php

namespace App\DataFixtures;

use App\Service\IGDBHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
	private IGDBHelper $IGDBHelper;

	public function __construct (IGDBHelper $IGDBHelper) {
		$this->IGDBHelper = $IGDBHelper;
	}

	/**
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager)
    {
        $this->IGDBHelper->refreshTokenInDatabase();
    }
}
