<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class InactiveUserException extends AuthenticationException
{
    public function getMessageKey()
    {
        return "Votre compte est désactivé. Veuillez contacter l'administrateur pour plus d'informations...";
    }
}