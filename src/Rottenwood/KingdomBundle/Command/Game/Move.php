<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Entity\User;

class Move implements GameCommandInterface {

    /** @var User */
    private $user;
    /** @var array */
    private $attributes;

    /**
     * @param User  $user
     * @param array $attributes
     */
    public function __construct(User $user, array $attributes) {
        $this->user = $user;
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function execute() {
        return [];
    }
}
