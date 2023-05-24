<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class CustomBadCredentialsException extends BadCredentialsException
{
    public function getMessageKey()
    {
        return 'Identifiant ou mot de passe incorrect.';
    }
}