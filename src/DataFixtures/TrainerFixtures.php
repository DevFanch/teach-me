<?php

namespace App\DataFixtures;

use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrainerFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRAINER_REFERENCE_PREFIX = 'trainer-';
    public const TRAINERS = [
        ['Bart', 'Simpson'],
        ['Lisa', 'Simpson'],
        ['Maggie', 'Simpson'],
        ['Homer', 'Simpson'],
        ['Marge', 'Simpson'],
        ['Ned', 'Flanders'],
        ['Krusty', 'the Clown'],
        ['Milhouse', 'Van Houten'],
        ['Edna', 'Krabappel'],
        ['Apu', 'Nahasapeemapetilon'],
        ['Moe', 'Szyslak'],
        ['Lenny', 'Leonard'],
        ['Montgomery', 'Burns'],
        ['Seymour', 'Skinner'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TRAINERS as $index => $trainerData) {
            $trainer = new Trainer();
            $trainer->setFirstName($trainerData[0]);
            $trainer->setLastName($trainerData[1]);
            $manager->persist($trainer);

            $this->addReference(self::TRAINER_REFERENCE_PREFIX . $index, $trainer);
            // debug
            // echo "Ajout de la référence " . self::TRAINER_REFERENCE_PREFIX . $index . "\n";
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
