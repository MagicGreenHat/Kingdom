<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Doctrine\ORM\EntityManager;
use Rottenwood\KingdomBundle\Entity\User;

interface GameCommandInterface {

    /**
     * @param User          $user
     * @param string        $parameters
     * @param EntityManager $entityManager
     */
    public function __construct(User $user, $parameters, $entityManager);

    /**
     * @return string
     */
    public function execute();
}
