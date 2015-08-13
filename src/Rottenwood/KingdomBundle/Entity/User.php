<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;

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
     * @ORM\Column(name="character_name", type="string", length=25)
     * @var string
     */
    protected $name;

    /**
     * Комната в которой находится персонаж
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(name="room", referencedColumnName="id", nullable=false)
     * @var Room
     */
    private $room;

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
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
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
     * Одетые на персонаже вещи
     * @return Item[]
     */
    public function getEquipment() {
        return array_filter([
            Item::USER_SLOT_HEAD =>  $this->getHeadSlot(),
            Item::USER_SLOT_AMULET => $this->getAmuletSlot(),
            Item::USER_SLOT_BODY => $this->getBodySlot(),
            Item::USER_SLOT_CLOAK => $this->getCloakSlot(),
            Item::USER_SLOT_WEAPON => $this->getWeaponSlot(),
            Item::USER_SLOT_LEFT_HAND => $this->getLeftHandSlot(),
            Item::USER_SLOT_GLOVES => $this->getGlovesSlot(),
            Item::USER_SLOT_RING_FIRST => $this->getRingFirstSlot(),
            Item::USER_SLOT_RING_SECOND => $this->getRingSecondSlot(),
            Item::USER_SLOT_LEGS => $this->getLegsSlot(),
            Item::USER_SLOT_BOOTS => $this->getBootsSlot(),
        ]);
    }
}
