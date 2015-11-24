<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Infrastructure\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface GameCommandInterface {

    /**
     * @param User               $user
     * @param string             $commandName
     * @param string             $parameters
     * @param ContainerInterface $container
     */
    public function __construct(User $user, $commandName, $parameters, ContainerInterface $container);

    /**
     * @return CommandResponse
     */
    public function execute();
}
