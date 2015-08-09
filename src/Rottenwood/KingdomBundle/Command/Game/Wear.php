<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;

/**
 * Одеть предмет
 */
class Wear extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $result = new CommandResponse('wear');

        $parameters = explode(':', $this->parameters);
        $itemId = $parameters[0];
        $slot = $parameters[1];

        if ($slot == Item::USER_SLOT_HEAD) {
        } elseif ($slot == Item::USER_SLOT_AMULET) {
        } elseif ($slot == Item::USER_SLOT_BODY) {
        } elseif ($slot == Item::USER_SLOT_CLOAK) {
        } elseif ($slot == Item::USER_SLOT_WEAPON) {
        } elseif ($slot == Item::USER_SLOT_LEFT_HAND) {
        } elseif ($slot == Item::USER_SLOT_GLOVES) {
        } elseif ($slot == Item::USER_SLOT_RING_FIRST) {
        } elseif ($slot == Item::USER_SLOT_RING_SECOND) {
        } elseif ($slot == Item::USER_SLOT_LEGS) {
        } elseif ($slot == Item::USER_SLOT_BOOTS) {

        };

        return $result;
    }
}
