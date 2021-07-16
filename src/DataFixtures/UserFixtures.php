<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
	private UserPasswordHasherInterface $encoder;

	public function __construct(UserPasswordHasherInterface $encoder)
	{
		$this->encoder = $encoder;
	}

    public function load(ObjectManager $manager)
    {

		for ($i = 0; $i < 20; $i++) {

			$user = new User('testuser' . $i+1 . '@example.com');
			$password = $this->encoder->hashPassword($user, 'password');
			$user->setPassword($password);

			$manager->persist($user);
		}

        $manager->flush();
    }
}
