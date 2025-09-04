<?php

namespace App\Security\Voter;

use App\Entity\Course;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CourseVoter extends Voter
{
    public const EDIT = 'COURSE_EDIT';
    public const VIEW = 'COURSE_VIEW';
    public const DELETE = 'COURSE_DELETE';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Course;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                $this->canEdit($subject, $user);
                // return true or false
                break;

            case self::VIEW:
                // logic to determine if the user can VIEW
                $this->canView();
                // return true or false
                break;

            case self::DELETE:
                // logic to determine if the user can DELETE
                $this->canDelete();
                // return true or false
                break;
        }

        return false;
    }

    private function canEdit(Course $course, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN') || $course->getAuthor() === $user;
    }
    private function canView(): bool
    {
        return $this->security->isGranted('PUBLIC_ACCESS');
    }
    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
