<?php

namespace App\DataFixtures;

use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TrainerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // for ($i = 1; $i <= 10; $i++) {
        //     $trainer = new Trainer();
        //     $trainer->setFirstname($faker->firstNameMale());
        //     $trainer->setLastname($faker->lastName());
        //     $manager->persist($trainer);

        //     $this->addReference('trainer-' . $i, $trainer);
        // }

        $trainer = new Trainer();
        $trainer->setFirstname($faker->firstNameMale());
        $trainer->setLastname($faker->lastName());
        $manager->persist($trainer);

        $this->addReference('trainer-1', $trainer);

        $trainer2 = new Trainer();
        $trainer2->setFirstname($faker->firstNameMale());
        $trainer2->setLastname($faker->lastName());
        $manager->persist($trainer2);

        $this->addReference('trainer-2', $trainer2);

        $trainer3 = new Trainer();
        $trainer3->setFirstname($faker->firstNameMale());
        $trainer3->setLastname($faker->lastName());
        $manager->persist($trainer3);
        
        $this->addReference('trainer-3', $trainer3);

        $manager->flush();
    }

    // public static function getGroups(): array
    // {
    //     return ['group1'];
    // }
}
