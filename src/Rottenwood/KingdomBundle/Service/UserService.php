<?php

namespace Rottenwood\KingdomBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Monolog\Logger;
use Rottenwood\KingdomBundle\Entity\Human;
use Rottenwood\KingdomBundle\Entity\Infrastructure\InventoryItemRepository;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\Infrastructure\ItemRepository;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomRepository;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\Infrastructure\User;
use Rottenwood\KingdomBundle\Entity\Infrastructure\HumanRepository;
use Rottenwood\KingdomBundle\Exception\ItemNotFound;
use Rottenwood\KingdomBundle\Exception\NotEnoughItems;
use Rottenwood\KingdomBundle\Exception\RoomNotFound;
use Rottenwood\KingdomBundle\Redis\RedisClientInterface;
use Snc\RedisBundle\Client\Phpredis\Client;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

class UserService
{

    /** @var KernelInterface */
    private $kernel;
    /** @var \Redis */
    private $redis;
    /** @var HumanRepository */
    private $humanRepository;
    /** @var InventoryItemRepository */
    private $inventoryItemRepository;
    /** @var Logger */
    private $logger;
    /** @var RoomRepository */
    private $roomRepository;
    /** @var ItemRepository */
    private $itemRepository;

    /**
     * @param KernelInterface         $kernel
     * @param Client                  $redis
     * @param Logger                  $logger
     * @param HumanRepository          $humanRepository
     * @param InventoryItemRepository $inventoryItemRepository
     * @param RoomRepository          $roomRepository
     * @param ItemRepository          $itemRepository
     */
    public function __construct(
        KernelInterface $kernel,
        Client $redis,
        Logger $logger,
        HumanRepository $humanRepository,
        InventoryItemRepository $inventoryItemRepository,
        RoomRepository $roomRepository,
        ItemRepository $itemRepository
    ) {
        $this->redis = $redis;
        $this->logger = $logger;
        $this->humanRepository = $humanRepository;
        $this->inventoryItemRepository = $inventoryItemRepository;
        $this->kernel = $kernel;
        $this->roomRepository = $roomRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * Запрос ID всех онлайн игроков в комнате
     * @param Room  $room
     * @param array $excludePlayerIds
     * @return int[]
     */
    public function getOnlineUsersIdsInRoom(Room $room, $excludePlayerIds = [])
    {
        return array_map(
            function (User $user) {
                return $user->getId();
            },
            $this->getOnlineUsersInRoom($room, $excludePlayerIds)
        );
    }

    /**
     * Запрос всех онлайн игроков в комнате
     * @param Room      $room
     * @param int|array $excludePlayerIds
     * @return Human[]
     */
    public function getOnlineUsersInRoom(Room $room, $excludePlayerIds = [])
    {
        if (!is_array($excludePlayerIds)) {
            $excludePlayerIds = [$excludePlayerIds];
        }

        return $this->humanRepository->findOnlineByRoom($room, $this->getOnlineUsersIds(), $excludePlayerIds);
    }

    /**
     * Запрос id всех игроков онлайн из redis
     * @return int[]
     */
    public function getOnlineUsersIds()
    {
        return $this->redis->smembers(RedisClientInterface::ONLINE_LIST);
    }

    /**
     * @param array $userIds
     * @return array
     */
    public function getSessionsByUserIds(array $userIds)
    {
        return array_values($this->redis->hmget(RedisClientInterface::ID_SESSION_HASH, $userIds));
    }

    /**
     * Передать один или несколько предметов другому персонажу
     * @param User $userFrom
     * @param User $userTo
     * @param Item|Item[] $items
     * @param int  $quantityToGive Сколько предметов передать
     * @return bool
     * @throws \Exception
     */
    public function giveItems(User $userFrom, User $userTo, $items, $quantityToGive = 1)
    {
        $items = $this->prepareItemsArray($items);

        try {
            $this->dropItems($userFrom, $items, $quantityToGive);
        } catch (\Exception $exception) {
            if ($exception instanceof ItemNotFound || $exception instanceof NotEnoughItems) {
                return false;
            } else {
                throw $exception;
            }
        }

        $this->takeItems($userTo, $items, $quantityToGive);

        $this->logGivenItems($userFrom, $userTo, $items, $quantityToGive);

        return true;
    }

    /**
     * Выбросить один или несколько предметов
     * @param User        $user
     * @param Item|Item[] $items
     * @param int         $quantityToDrop Сколько предметов выбросить
     * @return int Количество оставшихся предметов
     * @throws ItemNotFound
     * @throws NotEnoughItems
     */
    public function dropItems(User $user, $items, $quantityToDrop)
    {
        $items = $this->prepareItemsArray($items);

        $inventoryItems = $this->inventoryItemRepository->findByUser($user);
        $inventoryItemCollection = new ArrayCollection($inventoryItems);

        foreach ($items as $item) {
            $criteria = Criteria::create();
            $criteria->where(Criteria::expr()->eq('item', $item));

            $collectedInventoryItem = $inventoryItemCollection->matching($criteria);

            if ($collectedInventoryItem->isEmpty()) {
                throw new ItemNotFound;
            }

            $inventoryItem = $collectedInventoryItem->first();
            $itemQuantity = $inventoryItem->getQuantity();
            $itemQuantityAfterDrop = $itemQuantity - $quantityToDrop;

            if ($itemQuantityAfterDrop == 0) {
                $this->inventoryItemRepository->remove($inventoryItem);
            } elseif ($itemQuantityAfterDrop > 0) {
                $inventoryItem->setQuantity($itemQuantityAfterDrop);
            } else {
                throw new NotEnoughItems;
            }
        }

        $this->inventoryItemRepository->flush();

        $this->logDroppedItems($user, $items, $quantityToDrop);

        return true;
    }

    /**
     * Взять один или несколько предметов
     * @param User $user
     * @param Item|Item[] $items
     * @param int  $quantityToTake Сколько предметов взять
     */
    public function takeItems(User $user, $items, $quantityToTake = 1)
    {
        $itemsToTake = $this->prepareItemsArray($items);

        $inventoryItems = $this->inventoryItemRepository->findByUser($user);
        $inventoryItemCollection = new ArrayCollection($inventoryItems);

        foreach ($itemsToTake as $itemToTake) {
            $criteria = Criteria::create();
            $criteria->where(Criteria::expr()->eq('item', $itemToTake));

            $collectedInventoryItem = $inventoryItemCollection->matching($criteria);

            if ($collectedInventoryItem->count() === 1) {
                $inventoryItem = $collectedInventoryItem->first();
                $quantity = $inventoryItem->getQuantity() + $quantityToTake;
                $inventoryItem->setQuantity($quantity);
            } elseif ($collectedInventoryItem->count() === 0) {
                $inventoryItem = new InventoryItem($user, $itemToTake, $quantityToTake);
                $this->inventoryItemRepository->persist($inventoryItem);
            } else {
                throw new \RuntimeException('Найдено более одного предмета');
            }
        }

        $this->inventoryItemRepository->flush();

        $this->logObtainedItems(
            $user,
            $itemsToTake,
            $quantityToTake
        );
    }

    /**
     * Установка рэндомного аватара
     * @return string
     */
    public function pickAvatar()
    {
        $finder = new Finder();

        $prefix = 'male';
        $avatarPath = $this->kernel->getRootDir() . '/../web/img/avatars/' . $prefix;

        $files = $finder->files()->in($avatarPath);

        $avatars = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $avatars[] = $file->getBasename('.jpg');
        }

        $avatar = $prefix . '/' . $avatars[array_rand($avatars)];

        return $avatar;
    }

    /**
     * Транслитерация и конвертация строки, удаление цифр
     * @param string $string
     * @return string
     */
    public function transliterate($string)
    {
        $englishLetters = implode('', array_keys($this->getAlphabet()));
        $cyrillicLetters = 'абвгдеёжзиклмнопрстуфхцчшщьыъэюяАБВГДЕЁЖЗИКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯ';
        $pattern = '[^' . preg_quote($englishLetters . $cyrillicLetters, '/') . ']';

        $stringWithoutSpecialChars = mb_ereg_replace($pattern, '', $string);

        $cyrillicString = mb_convert_case(
            strtr($stringWithoutSpecialChars, $this->getAlphabet()),
            MB_CASE_TITLE,
            'UTF-8'
        );

        return $cyrillicString;
    }

    /**
     * Массив соответствия русских букв латинским
     * @return string[]
     */
    private function getAlphabet()
    {
        return [
            'a' => 'а', 'b' => 'б', 'c' => 'ц', 'd' => 'д', 'e' => 'е',
            'f' => 'ф', 'g' => 'г', 'h' => 'х', 'i' => 'ай', 'j' => 'дж',
            'k' => 'к', 'l' => 'л', 'm' => 'м', 'n' => 'н', 'o' => 'о',
            'p' => 'п', 'q' => 'к', 'r' => 'р', 's' => 'с', 't' => 'т',
            'u' => 'у', 'v' => 'в', 'w' => 'в', 'x' => 'кс', 'y' => 'й',
            'z' => 'з', 'A' => 'А', 'B' => 'Б', 'C' => 'Ц', 'D' => 'Д',
            'E' => 'Е', 'F' => 'Ф', 'G' => 'Г', 'H' => 'Х', 'I' => 'Ай',
            'J' => 'Дж', 'K' => 'К', 'L' => 'Л', 'M' => 'М', 'N' => 'Н',
            'O' => 'О', 'P' => 'П', 'Q' => 'К', 'R' => 'Р', 'S' => 'С',
            'T' => 'Т', 'U' => 'Ю', 'V' => 'В', 'W' => 'В', 'X' => 'Кс',
            'Y' => 'Й', 'Z' => 'З',
        ];
    }

    /**
     * Стартовая комната при создании персонажа
     * @return Room
     * @throws RoomNotFound
     */
    public function getStartRoom()
    {
        $startRoom = $this->roomRepository->findOneByXandY(0, 0);

        if (!$startRoom) {
            throw new RoomNotFound();
        }

        return $startRoom;
    }

    /**
     * Стартовые предметы при создании персонажа
     * @param User $user
     */
    public function giveStarterItems(User $user)
    {
        $starterItemsIds = [
            'newbie-boots',
            'newbie-legs',
            'newbie-shirt',
            'tester-sword',
        ];

        $items = $this->itemRepository->findSeveralByIds($starterItemsIds);

        $this->takeItems($user, $items);
    }

    /**
     * Подготовка массива предметов
     * @param $items
     * @return Item[]
     * @throws NotEnoughItems
     */
    public function prepareItemsArray($items)
    {
        $preparedItems = [];
        if (is_array($items) && current($items) instanceof Item) {
            $preparedItems = $items;
        } elseif ($items instanceof Item) {
            $preparedItems[] = $items;
        } else {
            throw new \RuntimeException('$items must be Item or array of Items entity');
        }

        if (empty($preparedItems)) {
            throw new NotEnoughItems('Не передано ни одного предмета для действия');
        }

        return $preparedItems;
    }

    /**
     * @param User   $user
     * @param Item[] $itemsToTake
     * @param int    $quantityToTake
     */
    private function logObtainedItems(User $user, array $itemsToTake, $quantityToTake)
    {
        /** @var Item $item */
        foreach ($itemsToTake as $item) {
            $this->logger->info(
                sprintf(
                    '[%d]%s взял предмет: [%d]%s x %d шт.',
                    $user->getId(),
                    $user->getName(),
                    $item->getId(),
                    $item->getName(),
                    $quantityToTake
                )
            );
        }
    }

    /**
     * @param User   $userFrom
     * @param User   $userTo
     * @param Item[] $items
     * @param int    $quantityToGive
     */
    private function logGivenItems(User $userFrom, User $userTo, array $items, $quantityToGive)
    {
        /** @var Item $item */
        foreach ($items as $item) {
            $this->logger->info(
                sprintf(
                    '[%d]%s передал [%d]%s предмет: [%d]%s x %d шт.',
                    $userFrom->getId(),
                    $userFrom->getName(),
                    $userTo->getId(),
                    $userTo->getName(),
                    $item->getId(),
                    $item->getName(),
                    $quantityToGive
                )
            );
        }
    }

    /**
     * @param User   $user
     * @param Item[] $items
     * @param int    $quantityToDrop
     */
    private function logDroppedItems($user, array $items, $quantityToDrop)
    {
        foreach ($items as $item) {
            $this->logger->info(
                sprintf(
                    '[%d]%s выбросил предмет: [%d]%s x %d шт.',
                    $user->getId(),
                    $user->getName(),
                    $item->getId(),
                    $item->getName(),
                    $quantityToDrop
                )
            );
        }
    }
}
