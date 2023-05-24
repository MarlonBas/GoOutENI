<?php


namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;

class CustomUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {

        if (!$user->isActif()) {
            throw new InactiveUserException('Votre compte est désactivé.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // Pas d'action nécessaire après l'authentification
    }
}