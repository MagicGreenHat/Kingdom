<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Entity\User;

class AbstractGameCommand implements GameCommandInterface {

    /** @var User */
    protected $user;
    /** @var string */
    protected $parameters;

    /**
     * @param User  $user
     * @param string $parameters
     */
    public function __construct(User $user, $parameters) {
        $this->user = $user;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function execute() {
        return $this->parameters;
    }
}
