<?php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Track
{
    public function __construct(private readonly MailerInterface $mailer) {}

    public function trackUser(UserInterface $user): void
    {
        // Add new email adress into end of file
        
        file_put_contents('track.txt', $user->getEmail());

        // Send email
        $message = new TemplatedEmail();
        $message->from('no-reply@teach-me-log.com')
            ->to('admin@teach-me.com')
            ->subject('Nouvel utilisateur créé depuis le site Teach-me')
            ->html('<h1>Nouvel utilisateur</h1>email: ' . $user->getEmail());
        $this->mailer->send($message);
    }
}
