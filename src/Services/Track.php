<?php

namespace App\Services;

use Symfony\Component\Security\Core\User\UserInterface;

class Track
{

   public function trackUser(UserInterface $user): void {
    // Persist new email adress into file
    file_put_contents('track.txt', $user->getEmail());

        $bodytag = str_ireplace("%body%", "black", "<body text=%BODY%>");
    }
}