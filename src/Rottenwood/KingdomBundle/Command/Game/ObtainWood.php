<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Items\ResourceWood;

/**
 * Добыча древесины
 * Применение в js: Kingdom.Websocket.command('obtainWood')
 */
class ObtainWood extends AbstractGameCommand {

    private $quantityToObtain = 1;
    /**
     * @return CommandResponse
     */
    public function execute() {
        $resourceRepository = $this->container->get('kingdom.room_resource_repository');
        $resources = $resourceRepository->findByRoom($this->user->getRoom());
        $userService = $this->container->get('kingdom.user_service');

        $result = [];
        foreach ($resources as $resource) {
            $resourceItem = $resource->getItem();

            if ($resourceItem instanceof ResourceWood) {
                $resource->reduceQuantity($this->quantityToObtain);
                $result['obtained'] = $this->quantityToObtain;
                $userService->takeItem($this->user, $resourceItem, $this->quantityToObtain);
                $result['resources'][$resourceItem->getId()] = $resource->getQuantity();
            }
        }

        $resourceRepository->flush();

        $this->result->setData($result);

        return $this->result;
    }
}
