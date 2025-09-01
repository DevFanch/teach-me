<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trainer;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Psr\Log\LoggerInterface;

class CourseFixtures extends Fixture {
    public function __construct(LoggerInterface $logger) {}

    public function load(ObjectManager $manager): void
    {
        $course = new Course();
        $course->setName('Symfony');
        $course->setContent('Le développement web côté serveur (avec Symfony)');
        $course->setDuration(10);
        $course->setPublished(true);
        // Usage d'un référence ex 
        $course->setCategory($this->getReference('category-dev', Category::class));
        // Création d'une référence pour les trainers
        // $trainer = $this->getReference('trainer-' . mt_rand(1, 10), Trainer::class);
        $trainer = $this->getReference('trainer-1', Trainer::class);
        $course->addTrainer($trainer);
        $manager->persist($course);

        $course = new Course();
        $course->setName('PHP');
        $course->setContent('Le développement web côté serveur (avec PHP)');
        $course->setDuration(5);
        $course->setPublished(true);
        $course->setCategory($this->getReference('category-dev', Category::class));
        // $trainer = $this->getReference('trainer-' . mt_rand(1, 10), Trainer::class);
        $trainer = $this->getReference('trainer-2', Trainer::class);
        $course->addTrainer($trainer);
        $manager->persist($course);

        $course = new Course();
        $course->setName('Apache');
        $course->setContent('Administration d\'un serveur Apache sous Linux');
        $course->setDuration(5);
        $course->setPublished(true);
        $course->setCategory($this->getReference('category-sys', Category::class));
        // $trainer = $this->getReference('trainer-' . mt_rand(1, 10), Trainer::class);
        $trainer = $this->getReference('trainer-3', Trainer::class);
        $course->addTrainer($trainer);
        $manager->persist($course);

        // Création de 30 cours supplémentaires
        // for ($i = 1; $i <= 30; $i++) {
        //     $course = new Course();
        //     $course->setName("Cours $i");
        //     $course->setContent("Description du cours $i");
        //     $course->setDuration(mt_rand(1, 10));
        //     $course->setPublished(false);
        //     $category = $this->getReference('category-dev', Category::class);
        //     $course->setCategory($category);
        //     $trainer = $this->getReference('trainer-' . mt_rand(1, 10), Trainer::class);
        //     $course->addTrainer($trainer);
        //     $manager->persist($course);
        // }
        $manager->flush();
    }

    // public static function getGroups(): array
    // {
    //     return ['group2'];
    // }
}
