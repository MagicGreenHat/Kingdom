<?php

namespace Rottenwood\KingdomBundle\Exception;

class WrongSlot extends \Exception {

    /**
     * @param string $slot
     */
    public function __construct($slot) {
        parent::__construct(sprintf('Слот "%s" не существует или не разрешен', $slot));
    }
}
