<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Doctrine\ORM\EntityManager;
use Rottenwood\KingdomBundle\Entity\User;

class AbstractGameCommand implements GameCommandInterface {

    /** @var User */
    protected $user;
    /** @var string */
    protected $parameters;
    /** @var EntityManager */
    private $entityManager;

    /**
     * @param User          $user
     * @param string        $parameters
     * @param EntityManager $entityManager
     */
    public function __construct(User $user, $parameters, $entityManager) {
        $this->user = $user;
        $this->parameters = $parameters;
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function execute() {
        return '';
    }
}
