<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;

/**
 * Снять предмет
 */
class Remove extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $result = new CommandResponse('remove');

        if ($this->parameters == Item::USER_SLOT_HEAD) {
            $this->user->setHeadSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_AMULET) {
            $this->user->setAmuletSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_BODY) {
            $this->user->setBodySlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_CLOAK) {
            $this->user->setCloakSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_WEAPON) {
            $this->user->setWeaponSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_LEFT_HAND) {
            $this->user->setLeftHandSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_GLOVES) {
            $this->user->setGlovesSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_RING_FIRST) {
            $this->user->setRingFirstSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_RING_SECOND) {
            $this->user->setRingSecondSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_LEGS) {
            $this->user->setLegsSlot(null);
        } elseif ($this->parameters == Item::USER_SLOT_BOOTS) {
            $this->user->setBootsSlot(null);
        };

        $this->container->get('kingdom.user_repository')->flush();
        $result->setData(['slot' => $this->parameters]);

        return $result;
    }
}
