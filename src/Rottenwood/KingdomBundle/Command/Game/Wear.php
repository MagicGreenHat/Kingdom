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

        $inventoryItemRepository = $this->container->get('kingdom.inventory_item_repository');
        $item = $inventoryItemRepository->findOneByUserAndItemId($this->user, $itemId)->getItem();

        if ($slot == Item::USER_SLOT_HEAD) {
            $this->user->setHeadSlot($item);
        } elseif ($slot == Item::USER_SLOT_AMULET) {
            $this->user->setAmuletSlot($item);
        } elseif ($slot == Item::USER_SLOT_BODY) {
            $this->user->setBodySlot($item);
        } elseif ($slot == Item::USER_SLOT_CLOAK) {
            $this->user->setCloakSlot($item);
        } elseif ($slot == Item::USER_SLOT_WEAPON) {
            $this->user->setWeaponSlot($item);
        } elseif ($slot == Item::USER_SLOT_LEFT_HAND) {
            $this->user->setLeftHandSlot($item);
        } elseif ($slot == Item::USER_SLOT_GLOVES) {
            $this->user->setGlovesSlot($item);
        } elseif ($slot == Item::USER_SLOT_RING_FIRST) {
            $this->user->setRingFirstSlot($item);
        } elseif ($slot == Item::USER_SLOT_RING_SECOND) {
            $this->user->setRingSecondSlot($item);
        } elseif ($slot == Item::USER_SLOT_LEGS) {
            $this->user->setLegsSlot($item);
        } elseif ($slot == Item::USER_SLOT_BOOTS) {
            $this->user->setBootsSlot($item);
        };

        $inventoryItemRepository->flush();

        $result->setData(
            [
                'itemName'       => $item->getName(),
                'itemName4'      => $item->getName4(),
                'description'    => $item->getDescription(),
                'availableSlots' => $item->getSlots(),
                'pic'            => $item->getPicture(),
                'slot'           => $slot,
            ]
        );

        return $result;
    }
}
