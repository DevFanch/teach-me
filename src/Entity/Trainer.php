<?php

namespace App\Entity;

use App\Repository\TrainerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TrainerRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['firstname', 'lastname'])]
class Trainer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le prénom du formateur est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 40,
        minMessage: 'Le prénom du formateur doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le prénom du formateur doit contenir au plus {{ limit }} caractères'
    )]
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: 'Le nom du formateur est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 40,
        minMessage: 'Le nom du formateur doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom du formateur doit contenir au plus {{ limit }} caractères'
    )]
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $modifiedAt = null;

    /**
     * @var Collection<int, Course>
     */
    #[ORM\ManyToMany(targetEntity: Course::class, mappedBy: 'trainer')]
    private Collection $courses;

    public function __construct()
    {
        /**
         * @var Collection<int, Course>
         */
        $this->courses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    #[ORM\PreUpdate]
    public function setModifiedAt(): static
    {
        $this->modifiedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addTrainer($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            $course->removeTrainer($this);
        }

        return $this;
    }

    public function fullName(): string
    {
        return $this->lastname . ' ' . $this->firstname;
    }
}
