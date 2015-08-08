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

    /**
     * Слот одежды: голова
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_head", referencedColumnName="id")
     * @var Item
     */
    private $headSlot;

    /**
     * Слот одежды: амулет
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_amulet", referencedColumnName="id")
     * @var Item
     */
    private $amuletSlot;

    /**
     * Слот одежды: тело
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_body", referencedColumnName="id")
     * @var Item
     */
    private $bodySlot;

    /**
     * Слот одежды: плащ
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_cloak", referencedColumnName="id")
     * @var Item
     */
    private $cloakSlot;

    /**
     * Слот одежды: оружие или инструмент
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_weapon", referencedColumnName="id")
     * @var Item
     */
    private $weaponSlot;

    /**
     * Слот одежды: в левой руке
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_left_hand", referencedColumnName="id")
     * @var Item
     */
    private $leftHandSlot;

    /**
     * Слот одежды: перчатки
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_gloves", referencedColumnName="id")
     * @var Item
     */
    private $glovesSlot;

    /**
     * Слот одежды: первое кольцо
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_ring_first", referencedColumnName="id")
     * @var Item
     */
    private $ringFirstSlot;

    /**
     * Слот одежды: второе кольцо
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_ring_second", referencedColumnName="id")
     * @var Item
     */
    private $ringSecondSlot;

    /**
     * Слот одежды: ноги
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_legs", referencedColumnName="id")
     * @var Item
     */
    private $legsSlot;

    /**
     * Слот одежды: обувь
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="slot_boots", referencedColumnName="id")
     * @var Item
     */
    private $bootsSlot;

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
     * @return Item
     */
    public function getHeadSlot() {
        return $this->headSlot;
    }

    /**
     * @param Item $headSlot
     */
    public function setHeadSlot($headSlot) {
        $this->headSlot = $headSlot;
    }

    /**
     * @return Item
     */
    public function getAmuletSlot() {
        return $this->amuletSlot;
    }

    /**
     * @param Item $amuletSlot
     */
    public function setAmuletSlot($amuletSlot) {
        $this->amuletSlot = $amuletSlot;
    }

    /**
     * @return Item
     */
    public function getBodySlot() {
        return $this->bodySlot;
    }

    /**
     * @param Item $bodySlot
     */
    public function setBodySlot($bodySlot) {
        $this->bodySlot = $bodySlot;
    }

    /**
     * @return Item
     */
    public function getCloakSlot() {
        return $this->cloakSlot;
    }

    /**
     * @param Item $cloakSlot
     */
    public function setCloakSlot($cloakSlot) {
        $this->cloakSlot = $cloakSlot;
    }

    /**
     * @return Item
     */
    public function getWeaponSlot() {
        return $this->weaponSlot;
    }

    /**
     * @param Item $weaponSlot
     */
    public function setWeaponSlot($weaponSlot) {
        $this->weaponSlot = $weaponSlot;
    }

    /**
     * @return Item
     */
    public function getLeftHandSlot() {
        return $this->leftHandSlot;
    }

    /**
     * @param Item $leftHandSlot
     */
    public function setLeftHandSlot($leftHandSlot) {
        $this->leftHandSlot = $leftHandSlot;
    }

    /**
     * @return Item
     */
    public function getGlovesSlot() {
        return $this->glovesSlot;
    }

    /**
     * @param Item $glovesSlot
     */
    public function setGlovesSlot($glovesSlot) {
        $this->glovesSlot = $glovesSlot;
    }

    /**
     * @return Item
     */
    public function getRingFirstSlot() {
        return $this->ringFirstSlot;
    }

    /**
     * @param Item $ringFirstSlot
     */
    public function setRingFirstSlot($ringFirstSlot) {
        $this->ringFirstSlot = $ringFirstSlot;
    }

    /**
     * @return Item
     */
    public function getRingSecondSlot() {
        return $this->ringSecondSlot;
    }

    /**
     * @param Item $ringSecondSlot
     */
    public function setRingSecondSlot($ringSecondSlot) {
        $this->ringSecondSlot = $ringSecondSlot;
    }

    /**
     * @return Item
     */
    public function getLegsSlot() {
        return $this->legsSlot;
    }

    /**
     * @param Item $legsSlot
     */
    public function setLegsSlot($legsSlot) {
        $this->legsSlot = $legsSlot;
    }

    /**
     * @return Item
     */
    public function getBootsSlot() {
        return $this->bootsSlot;
    }

    /**
     * @param Item $bootsSlot
     */
    public function setBootsSlot($bootsSlot) {
        $this->bootsSlot = $bootsSlot;
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
}
