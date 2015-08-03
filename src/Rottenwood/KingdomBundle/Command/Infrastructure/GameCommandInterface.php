<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

use Rottenwood\KingdomBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface GameCommandInterface {

    /**
     * @param User               $user
     * @param string             $parameters
     * @param ContainerInterface $container
     */
    public function __construct(User $user, $parameters, ContainerInterface $container);

    /**
     * @return CommandResponse
     */
    public function execute();
}
