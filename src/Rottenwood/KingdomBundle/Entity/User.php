<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Exception\ItemNotFound;
use Rottenwood\KingdomBundle\Exception\NotEnoughItems;

/**
 * Игровой персонаж
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\UserRepository")
 */
class User extends BaseUser {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * Комната в которой находится персонаж
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(name="room", referencedColumnName="id", nullable=false)
     * @var Room
     */
    private $room;

    /**
     * Предметы в инвентаре персонажа
     * @ORM\ManyToOne(targetEntity="InventoryItem")
     * @ORM\JoinColumn(name="items", referencedColumnName="id")
     * @var ArrayCollection
     */
    private $inventoryItems;

    public function __construct() {
        parent::__construct();

        $this->inventoryItems = new ArrayCollection();
    }

    /**
     * @return Room
     */
    public function getRoom() {
        return $this->room;
    }

    /**
     * @param Room $room
     */
    public function setRoom($room) {
        $this->room = $room;
    }

    /**
     * @return InventoryItem[]
     */
    public function getInventory() {
        return $this->inventoryItems->toArray();
    }

    /**
     * Передать предмет другому персонажу
     * @param Item $item
     * @param User $user
     * @param int  $quantityToGive Сколько предметов передать
     * @return bool
     * @throws \Exception
     */
    public function giveItem(Item $item, User $user, $quantityToGive = 1) {
        //TODO[Rottenwood]: Логирование

        try {
            $this->dropItem($item, $quantityToGive);
        } catch (\Exception $exception) {
            if ($exception instanceof ItemNotFound || $exception instanceof NotEnoughItems) {
                return false;
            } else {
                throw $exception;
            }
        }

        $user->takeItem($item, $quantityToGive);

        return true;
    }

    /**
     * Выбросить предмет
     * @param Item $item
     * @param int  $quantityToDrop Сколько предметов выбросить
     * @return int Количество оставшихся предметов
     * @throws ItemNotFound
     * @throws NotEnoughItems
     */
    public function dropItem(Item $item, $quantityToDrop) {
        //TODO[Rottenwood]: Логирование

        $inventoryItem = $this->getItemFromInventory($item);

        if (!$inventoryItem) {
            throw new ItemNotFound;
        }

        $itemQuantity = $inventoryItem->getQuantity();
        $itemQuantityAfterDrop = $itemQuantity - $quantityToDrop;

        if ($itemQuantityAfterDrop == 0) {
            $this->inventoryItems->remove($inventoryItem);
        } elseif ($itemQuantityAfterDrop > 0) {
            $inventoryItem->setQuantity($itemQuantityAfterDrop);
        } else {
            throw new NotEnoughItems;
        }

        return $itemQuantityAfterDrop;

    }

    /**
     * Взять предмет
     * @param Item $item
     * @param int  $quantityToTake Сколько предметов взять
     */
    public function takeItem(Item $item, $quantityToTake = 1) {
        //TODO[Rottenwood]: Логирование

        $inventoryItem = $this->getItemFromInventory($item);

        if ($inventoryItem) {
            $quantity = $inventoryItem->getQuantity() + $quantityToTake;
            $inventoryItem->setQuantity($quantity);
        } else {
            $inventoryItem = new InventoryItem($this, $item, $quantityToTake);
        }

        $this->inventoryItems->add($inventoryItem);
    }

    /**
     * Поиск предмета в инвентаре
     * @param Item $itemToFind
     * @return InventoryItem
     */
    private function getItemFromInventory(Item $itemToFind) {
        foreach ($this->inventoryItems as $inventoryItem) {
            if ($inventoryItem->getItem()->getId() == $itemToFind->getId()) {
                return $inventoryItem;
            }
        }

        return null;
    }
}
