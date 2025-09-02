<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE_PREFIX = 'user-';

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // User Admin
        $user = new User();
        $user->setEmail('admin@hell-demo.com');
        $user->setFirstName('Hell');
        $user->setLastName('Teacher');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'Hell123!'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsVerified(true);
        $manager->persist($user);
        
        // User Planner
        $user = new User();
        $user->setEmail('planner@hell-demo.com');
        $user->setFirstName('Planner');
        $user->setLastName('Teacher');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'Planner123!'));
        $user->setRoles(['ROLE_PLANNER']);
        $user->setIsVerified(true);
        $manager->persist($user);

        // Users User
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@hell-demo.com');
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName());
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'User123!'));
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(true);
            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE_PREFIX . $i, $user);
        }

        $manager->flush();
    }
}
