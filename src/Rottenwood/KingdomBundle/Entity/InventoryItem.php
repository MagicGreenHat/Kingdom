<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\Infrastructure\User;
use Rottenwood\KingdomBundle\Exception\WrongSlot;

/**
 * Предмет в инвентаре персонажа
 * @ORM\Table(
 *      name="users_items",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="inventory_user_item", columns={"user_id", "item_id"}),
 *          @ORM\UniqueConstraint(name="inventory_user_slot", columns={"user_id", "slot"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\InventoryItemRepository")
 */
class InventoryItem {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Персонаж
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @var User
     */
    private $user;

    /**
     * Предмет
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     * @var Item
     */
    private $item;

    /**
     * Количество предметов
     * @var int
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * Слот, в который одет предмет
     * @ORM\Column(name="slot", type="string", length=50, nullable=true)
     * @var string
     */
    private $slot;

    /**
     * @param User $user
     * @param Item $item
     * @param int  $quantity
     */
    public function __construct(User $user, Item $item, $quantity = 1) {
        $this->user = $user;
        $this->item = $item;
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return Item
     */
    public function getItem() {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getSlot() {
        return $this->slot;
    }

    /**
     * @param string $slot
     * @throws WrongSlot
     */
    public function setSlot($slot) {
        if (in_array($slot, Item::getAllSlotNames())) {
            $this->slot = $slot;
        } else {
            throw new WrongSlot($slot);
        }
    }

    /**
     * Удаление предмета из слота
     */
    public function removeSlot() {
        $this->slot = null;
    }
}
