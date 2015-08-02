<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Entity\User;

interface GameCommandInterface {

    /**
     * @param User  $user
     * @param string $parameters
     */
    public function __construct(User $user, $parameters);

    /**
     * @return string
     */
    public function execute();
}
