<?php
/**
 * @author: Rottenwood
 * @date  : 15.11.15 22:54
 */

namespace Rottenwood\UserBundle\Loggers;

use Monolog\Logger;
use Rottenwood\KingdomBundle\Entity\Infrastructure\User;

class RegistrationLogger extends Logger
{

    /**
     * @param User $user
     */
    public function logRegistration(User $user)
    {
        $this->info(
            sprintf(
                '[#%d] логин: %s, имя: %s, email: %s',
                $user->getId(),
                $user->getUsername(),
                $user->getName(),
                $user->getEmail()
            )
        );
    }
}