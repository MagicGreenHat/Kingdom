<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;

/**
 * Предмет в инвентаре персонажа
 * @ORM\Table(
 *      name="users_items",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="inventory_user_item", columns={"user_id", "item_id"})}
 * )
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\InventoryItemRepository")
 */
class InventoryItem {

    /**
     * Персонаж
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @var User
     */
    protected $user;

    /**
     * Предмет
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\Item")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     * @var Item
     */
    protected $item;

    /**
     * Количество предметов
     * @var int
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

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
}
