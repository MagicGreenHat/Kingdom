<?php

namespace Rottenwood\KingdomBundle\Command\Game;

interface GameCommandInterface {

    public function execute($userId, $command, array $attributes);
}
