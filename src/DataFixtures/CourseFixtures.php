<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    private array $courses = [
        [
            'name' => 'Symfony',
            'content' => 'Le développement web côté serveur (avec Symfony)',
            'duration' => 10,
            'category' => '0',
        ],
        [
            'name' => 'PHP',
            'content' => 'Le développement web côté serveur (avec PHP)',
            'duration' => 35,
            'category' => '1',
        ],
        [
            'name' => 'Apache',
            'content' => 'Administration d\'un serveur Apache sous Linux',
            'duration' => 15,
            'category' => '2',
        ],
        [
            'name' => 'Linux',
            'content' => 'Administration d\'un serveur Linux',
            'duration' => 15,
            'category' => '3',
        ],
        [
            'name' => 'Javascript',
            'content' => 'Le développement web côté client (avec Javascript)',
            'duration' => 90,
            'category' => '4',
        ],
        [
            'name' => 'Python',
            'content' => 'Le développement web côté serveur (avec Python)',
            'duration' => 15,
            'category' => '5',
        ],
        [
            'name' => 'Java',
            'content' => 'Le développement web côté serveur (avec Java)',
            'duration' => 280,
            'category' => '6',
        ],
        [
            'name' => 'Systeme & réseaux',
            'content' => 'Administration d\'un serveur Linux',
            'duration' => 15,
            'category' => '7',
        ],
        [
            'name' => 'Base de données',
            'content' => 'Le développement web côté serveur (avec Base de données)',
            'duration' => 35,
            'category' => '8',
        ],
        [
            'name' => 'DevOps',
            'content' => 'Le développement web côté serveur (avec DevOps)',
            'duration' => 35,
            'category' => '9',
        ],
        [
            'name' => 'Cloud',
            'content' => 'Le développement web côté serveur (avec Cloud)',
            'duration' => 35,
            'category' => '10',
        ],
        [
            'name' => 'Cybersécurité',
            'content' => 'Le développement web côté serveur (avec Cybersécurité)',
            'duration' => 35,
            'category' => '11',
        ]
    ];

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // D'abord, récupérez tous les formateurs disponibles
        $trainers = [];
        $i = 0;
        while (true) {
            try {
                $trainer = $this->getReference(TrainerFixtures::TRAINER_REFERENCE_PREFIX . $i, Trainer::class);
                $trainers[] = $trainer;
                $i++;
            } catch (\Exception $e) {
                // Si on ne trouve plus de formateurs, on sort de la boucle
                break;
            }
        }

        // Si aucun formateur n'est trouvé, affichons un avertissement
        if (empty($trainers)) {
            echo "ATTENTION: Aucun formateur n'a été trouvé. Assurez-vous que TrainerFixtures est bien chargé avant CourseFixtures.\n";
        }

        $trainerCount = count($trainers);

        foreach ($this->courses as $courseData) {
            $course = new Course();
            $course->setName($courseData['name']);
            $course->setContent($courseData['content']);
            $course->setDuration(intval($courseData['duration']));
            $course->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year', '-6 months')));
            $course->setPublished($this->faker->boolean(70));
            $course->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE_PREFIX . $courseData['category'], Category::class));

            // Si nous avons des formateurs disponibles
            if ($trainerCount > 0) {
                // Sélectionner un nombre aléatoire de formateurs entre 1 et 3
                $nbTrainers = min(mt_rand(1, 3), $trainerCount);

                // Mélanger le tableau pour obtenir des sélections aléatoires
                $selectedTrainers = $trainers;
                shuffle($selectedTrainers);

                // Assigner les formateurs au cours
                for ($j = 0; $j < $nbTrainers; $j++) {
                    $course->addTrainer($selectedTrainers[$j]);
                }
            }

            $manager->persist($course);
        }

        // Création de 30 fakes cours supplémentaires
        for ($i = 1; $i <= 30; $i++) {
            $course = new Course();
            $course->setName($this->faker->sentence(3));
            $course->setContent($this->faker->paragraph(3));
            $course->setDuration(mt_rand(1, 10));
            // Cette date doit être une DateTimeImmutable : createFromMutable between -1 year and - 6 months
            $course->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year', '-6 months')));
            // Cette date doit être une DateTimeImmutable : update now
            $course->setModifiedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween($course->getCreatedAt()->format('Y-m-d'), 'now')
            ));
            $course->setPublished($this->faker->boolean(70));
            // $course->setAuthor($this->getReference(UserFixtures::USER_REFERENCE_PREFIX . mt_rand(0, 9), User::class));
            // On récupère une catégorie aléatoire
            $randomCategory = mt_rand(0, count(CategoryFixtures::CATEGORIES) - 1);
            $course->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE_PREFIX . $randomCategory, Category::class));

            // Sélectionner un nombre aléatoire de formateurs
            $nbTrainers = min(mt_rand(1, 3), $trainerCount);

            // Créer un tableau d'indices de formateurs
            $trainerIndices = range(0, $trainerCount - 1);
            // Mélanger le tableau pour obtenir des sélections aléatoires
            shuffle($trainerIndices);

            // Prendre les n premiers indices
            for ($j = 0; $j < $nbTrainers; $j++) {
                try {
                    $trainer = $this->getReference(TrainerFixtures::TRAINER_REFERENCE_PREFIX . $trainerIndices[$j], Trainer::class);
                    $course->addTrainer($trainer);
                } catch (\Exception $e) {
                    // Ignorer les erreurs de référence et continuer
                    continue;
                }
            }

            $manager->persist($course);
        }

        $manager->flush();
    }

    // On dit que CourseFixtures dépend de CategoryFixtures et TrainerFixtures
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            TrainerFixtures::class,
        ];
    }
}
