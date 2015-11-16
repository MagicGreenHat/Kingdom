<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\Items\ResourceWood;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomResource;
use Rottenwood\KingdomBundle\Entity\RoomTypes\Grass;
use Rottenwood\KingdomBundle\Service\UserService;

/**
 * Добыча древесины
 * Применение в js: Kingdom.Websocket.command('obtainWood')
 */
class ObtainWood extends AbstractGameCommand
{

    private $quantityToObtain = 1;
    private $waitState = 5;

    /**
     * @return CommandResponse
     */
    public function execute()
    {
        $resourceRepository = $this->container->get('kingdom.room_resource_repository');
        $em = $resourceRepository->getEntityManager();
        $userService = $this->container->get('kingdom.user_service');
        $room = $this->user->getRoom();
        $resources = $resourceRepository->findByRoom($room);

        $result = [];
        foreach ($resources as $resource) {
            $resourceItem = $resource->getItem();
            $resourceQuantity = $resource->getQuantity();

            if ($resourceItem instanceof ResourceWood && $resourceQuantity > 0) {
                $result = $this->obtain($resource, $em, $result, $userService, $resourceItem, $room);
            } elseif ($resourceItem instanceof ResourceWood && $resourceQuantity === 0) {
                //TODO[Rottenwood]: Отвечать за удаление ресурсов из комнат должен листнер на postflush
                $em->remove($resource);
            }
        }

        $em->flush();

        $this->result->setData($result);

        return $this->result;
    }

    /**
     * Добыча ресурсов
     * //TODO[Rottenwood]: Рефакторинг. Перенести в ObtainService
     * @param RoomResource  $resource
     * @param EntityManager $em
     * @param array         $result
     * @param UserService   $userService
     * @param Item          $resourceItem
     * @param Room          $room
     * @return array
     */
    public function obtain(
        RoomResource $resource,
        EntityManager $em,
        array $result,
        UserService $userService,
        Item $resourceItem,
        Room $room
    )
    {
        if ($this->user->isBusy()) {
            $result['waitstate'] = $this->user->getWaitstate();

            return $result;
        }

        $this->setWaitstate($em);
        $this->reduceQuantity($em, $resource->getId(), $userService);

        $result['obtained'] = $this->quantityToObtain;

        $resourceQuantity = $resource->getQuantity();
        $result['resources'][$resourceItem->getId()] = $resourceQuantity;

        if ($resourceQuantity <= 0) {
            /** @var Grass[] $grassTypes */
            $grassTypes = $em->getRepository(Grass::class)->findAll();
            $grassType = $grassTypes[array_rand($grassTypes)];
            $room->setType($grassType);
            $result['typeChanged'] = true;

            $em->remove($resource);
        }

        return $result;
    }

    /**
     * Назначение вейтстейта игроку
     * @param EntityManager $em
     */
    private function setWaitstate(EntityManager $em)
    {
        $this->user->addWaitstate($this->waitState);
        $em->flush($this->user);
    }

    /**
     * Транзакция с локированием добываемого ресурса
     * @param EntityManager $em
     * @param int           $resourceId
     * @param UserService   $userService
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    private function reduceQuantity(EntityManager $em, $resourceId, UserService $userService)
    {
        $em->getConnection()->beginTransaction();

        try {
            /** @var RoomResource $resourceToUpdate */
            $resourceToUpdate = $em->find(RoomResource::class, $resourceId, LockMode::PESSIMISTIC_READ);
            $resourceToUpdate->reduceQuantity($this->quantityToObtain);

            $userService->takeItem($this->user, $resourceToUpdate->getItem(), $this->quantityToObtain);

            $em->persist($resourceToUpdate);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }
}
