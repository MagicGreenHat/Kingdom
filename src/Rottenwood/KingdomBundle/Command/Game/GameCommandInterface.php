<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Entity\User;

interface GameCommandInterface {

    /**
     * @param User  $user
     * @param array $attributes
     */
    public function __construct(User $user, array $attributes);

    /**
     * @return array
     */
    public function execute();
}
