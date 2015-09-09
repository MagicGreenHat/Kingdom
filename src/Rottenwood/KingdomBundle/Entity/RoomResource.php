<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;

/**
 * Недобытый природный ресурс
 * @ORM\Table(name="rooms_items")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomResourceRepository")
 */
class RoomResource {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Комната в которой находится ресурс
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false)
     * @var Room
     */
    private $room;

    /**
     * Предмет ресурса
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
     * @param Room $room
     * @param Item $item
     * @param int  $quantity
     */
    public function __construct(Room $room, Item $item, $quantity) {
        $this->room = $room;
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
     * @return Room
     */
    public function getRoom() {
        return $this->room;
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
     * @param int $amount
     */
    public function reduceQuantity($amount) {
        $this->quantity -= $amount;
    }
}
