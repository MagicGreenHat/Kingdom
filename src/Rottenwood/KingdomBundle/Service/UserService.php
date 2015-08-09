<?php

namespace Rottenwood\KingdomBundle\Service;

use Rottenwood\KingdomBundle\Entity\Infrastructure\InventoryItemRepository;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\User;
use Rottenwood\KingdomBundle\Entity\Infrastructure\UserRepository;
use Rottenwood\KingdomBundle\Exception\ItemNotFound;
use Rottenwood\KingdomBundle\Exception\NotEnoughItems;
use Rottenwood\KingdomBundle\Redis\RedisClientInterface;
use Snc\RedisBundle\Client\Phpredis\Client;

class UserService {

    /** @var RedisClientInterface */
    private $redis;
    /** @var UserRepository */
    private $userRepository;
    /** @var InventoryItemRepository */
    private $inventoryItemRepository;

    /**
     * @param Client                  $redis
     * @param UserRepository          $userRepository
     * @param InventoryItemRepository $inventoryItemRepository
     */
    public function __construct(Client $redis, UserRepository $userRepository, InventoryItemRepository $inventoryItemRepository) {
        $this->redis = $redis;
        $this->userRepository = $userRepository;
        $this->inventoryItemRepository = $inventoryItemRepository;
    }

    /**
     * Запрос всех онлайн игроков в комнате
     * @param Room  $room
     * @param array $excludePlayerIds
     * @return User[]
     */
    public function getOnlineUsersInRoom(Room $room, $excludePlayerIds = []) {
        if (!is_array($excludePlayerIds)) {
            $excludePlayerIds = [$excludePlayerIds];
        }

        return $this->userRepository->findOnlineByRoom($room, $this->getOnlineUsersIds(), $excludePlayerIds);
    }

    /**
     * Запрос ID всех онлайн игроков в комнате
     * @param Room  $room
     * @param array $excludePlayerIds
     * @return int[]
     */
    public function getOnlineUsersIdsInRoom(Room $room, $excludePlayerIds = []) {
        return array_map(
            function (User $user) {
                return $user->getId();
            },
            $this->getOnlineUsersInRoom($room, $excludePlayerIds)
        );
    }

    /**
     * Запрос id всех игроков онлайн из redis
     * @return int[]
     */
    public function getOnlineUsersIds() {
        return array_map(
            function ($player) {
                return json_decode($player, true)['id'];
            },
            $this->redis->hgetall(RedisClientInterface::CHARACTERS_HASH_NAME)
        );
    }

    /**
     * @param array $userIds
     * @return array
     */
    public function getSessionsByUserIds(array $userIds) {
        return array_values($this->redis->hmget(RedisClientInterface::ID_SESSION_HASH, $userIds));
    }

    /**
     * Передать предмет другому персонажу
     * @param User $userFrom
     * @param User $userTo
     * @param Item $item
     * @param int  $quantityToGive Сколько предметов передать
     * @return bool
     * @throws \Exception
     */
    public function giveItem(User $userFrom, User $userTo, Item $item, $quantityToGive = 1) {
        //TODO[Rottenwood]: Логирование

        try {
            $this->dropItem($userFrom, $item, $quantityToGive);
        } catch (\Exception $exception) {
            if ($exception instanceof ItemNotFound || $exception instanceof NotEnoughItems) {
                return false;
            } else {
                throw $exception;
            }
        }

        $this->takeItem($userTo, $item, $quantityToGive);

        return true;
    }

    /**
     * Выбросить предмет
     * @param User $user
     * @param Item $item
     * @param int  $quantityToDrop Сколько предметов выбросить
     * @return int Количество оставшихся предметов
     * @throws ItemNotFound
     * @throws NotEnoughItems
     */
    public function dropItem(User $user, Item $item, $quantityToDrop) {
        //TODO[Rottenwood]: Логирование

        $inventoryItem = $this->inventoryItemRepository->findOneByUserAndItemId($user, $item->getId());

        if (!$inventoryItem) {
            throw new ItemNotFound;
        }

        $itemQuantity = $inventoryItem->getQuantity();
        $itemQuantityAfterDrop = $itemQuantity - $quantityToDrop;

        if ($itemQuantityAfterDrop == 0) {
            $this->inventoryItemRepository->remove($inventoryItem);
        } elseif ($itemQuantityAfterDrop > 0) {
            $inventoryItem->setQuantity($itemQuantityAfterDrop);
        } else {
            throw new NotEnoughItems;
        }

        $this->inventoryItemRepository->flush($inventoryItem);

        return $itemQuantityAfterDrop;
    }

    /**
     * Взять предмет
     * @param User $user
     * @param Item $item
     * @param int  $quantityToTake Сколько предметов взять
     */
    public function takeItem(User $user, Item $item, $quantityToTake = 1) {
        //TODO[Rottenwood]: Логирование

        $inventoryItem = $this->inventoryItemRepository->findOneByUserAndItemId($user, $item->getId());

        if ($inventoryItem) {
            $quantity = $inventoryItem->getQuantity() + $quantityToTake;
            $inventoryItem->setQuantity($quantity);
        } else {
            $inventoryItem = new InventoryItem($user, $item, $quantityToTake);
            $this->inventoryItemRepository->persist($inventoryItem);
        }

        $this->inventoryItemRepository->flush($inventoryItem);
    }

    /**
     * Id одетых на персонаже вещей
     * @param User $user
     * @return int[]
     */
    public function getWornItemsIds(User $user) {
        $headSlot = $user->getHeadSlot();
        $amuletSlot = $user->getAmuletSlot();
        $bodySlot = $user->getBodySlot();
        $cloakSlot = $user->getCloakSlot();
        $weaponSlot = $user->getWeaponSlot();
        $leftHandSlot = $user->getLeftHandSlot();
        $glovesSlot = $user->getGlovesSlot();
        $ringFirstSlot = $user->getRingFirstSlot();
        $ringSecondSlot = $user->getRingSecondSlot();
        $legsSlot = $user->getLegsSlot();
        $bootsSlot = $user->getBootsSlot();

        return [
            Item::USER_SLOT_HEAD => $headSlot ? $headSlot->getId() : null,
            Item::USER_SLOT_AMULET => $amuletSlot ? $amuletSlot->getId() : null,
            Item::USER_SLOT_BODY => $bodySlot ? $bodySlot->getId() : null,
            Item::USER_SLOT_CLOAK => $cloakSlot ? $cloakSlot->getId() : null,
            Item::USER_SLOT_WEAPON => $weaponSlot ? $weaponSlot->getId() : null,
            Item::USER_SLOT_LEFT_HAND => $leftHandSlot ? $leftHandSlot->getId() : null,
            Item::USER_SLOT_GLOVES => $glovesSlot ? $glovesSlot->getId() : null,
            Item::USER_SLOT_RING_FIRST => $ringFirstSlot ? $ringFirstSlot->getId() : null,
            Item::USER_SLOT_RING_SECOND => $ringSecondSlot ? $ringSecondSlot->getId() : null,
            Item::USER_SLOT_LEGS => $legsSlot ? $legsSlot->getId() : null,
            Item::USER_SLOT_BOOTS => $bootsSlot ? $bootsSlot->getId() : null,
        ];
    }
}
