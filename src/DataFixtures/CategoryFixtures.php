<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName('Développement');
        $manager->persist($category);
        $this->addReference('category-dev', $category);

        $category2 = new Category();
        $category2->setName('Système & Réseaux');
        $manager->persist($category2);
        $this->addReference('category-sys', $category2);

        $manager->flush();
        // Création des références
    }

    // public static function getGroups(): array {
    //     return ['group1'];
    // }
}
