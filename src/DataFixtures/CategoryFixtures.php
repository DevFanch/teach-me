<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE_PREFIX = 'category-';
    public const CATEGORIES = [
        'Symfony',
        'PHP',
        'Apache',
        'Linux',
        'Javascript',
        'Python',
        'Java',
        'Systeme & réseaux',
        'Base de données',
        'DevOps',
        'Cloud',
        'Cybersécurité',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            // on ajoute une référence à la catégorie
            $this->addReference(self::CATEGORY_REFERENCE_PREFIX . $key, $category);
        }

        $manager->flush();
    }
}
