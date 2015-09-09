<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Items\ResourceWood;
use Rottenwood\KingdomBundle\Entity\RoomTypes\Grass;

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
        $em = $resourceRepository->getEntityManager();
        $userService = $this->container->get('kingdom.user_service');
        $room = $this->user->getRoom();
        $resources = $resourceRepository->findByRoom($room);

        $result = [];
        foreach ($resources as $resource) {
            $resourceItem = $resource->getItem();

            if ($resourceItem instanceof ResourceWood) {
                $resource->reduceQuantity($this->quantityToObtain);
                $result['obtained'] = $this->quantityToObtain;
                $userService->takeItem($this->user, $resourceItem, $this->quantityToObtain);

                $resourcesLeft = $resource->getQuantity();
                $result['resources'][$resourceItem->getId()] = $resourcesLeft;

                if ($resourcesLeft <= 0) {
                    /** @var Grass[] $grassTypes */
                    $grassTypes = $em->getRepository(Grass::class)->findAll();
                    $grassType = $grassTypes[array_rand($grassTypes)];
                	$room->setType($grassType);
                    $result['typeChanged'] = true;
                }
            }
        }

        $em->flush();

        $this->result->setData($result);

        return $this->result;
    }
}
