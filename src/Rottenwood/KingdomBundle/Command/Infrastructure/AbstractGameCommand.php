<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Infrastructure\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractGameCommand implements GameCommandInterface {

    /** @var User */
    protected $user;
    /** @var string */
    protected $parameters;
    /** @var ContainerInterface */
    protected $container;
    /** @var CommandResponse */
    protected $result;

    /**
     * @param User               $user
     * @param string             $commandName
     * @param string             $parameters
     * @param ContainerInterface $container
     */
    public function __construct(User $user, $commandName, $parameters, ContainerInterface $container) {
        $this->user = $user;
        $this->parameters = $parameters;
        $this->container = $container;
        $this->result = new CommandResponse($commandName);
    }

    abstract public function execute();
}
