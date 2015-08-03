<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

use Rottenwood\KingdomBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractGameCommand implements GameCommandInterface {

    /** @var User */
    protected $user;
    /** @var string */
    protected $parameters;
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param User               $user
     * @param string             $parameters
     * @param ContainerInterface $container
     */
    public function __construct(User $user, $parameters, ContainerInterface $container) {
        $this->user = $user;
        $this->parameters = $parameters;
        $this->container = $container;
    }
}
