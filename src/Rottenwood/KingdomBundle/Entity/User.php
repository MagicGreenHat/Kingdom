<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Игровой персонаж
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\UserRepository")
 */
class User extends BaseUser {

    const AVATAR_PATH = '/img/avatars/';
    const AVATAR_EXTENSION = 'jpg';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * Русское имя персонажа
     * @ORM\Column(name="character_name", type="string", length=25, unique=true)
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
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_head", referencedColumnName="id")
     * @var InventoryItem
     */
    private $headSlot;

    /**
     * Слот одежды: амулет
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_amulet", referencedColumnName="id")
     * @var InventoryItem
     */
    private $amuletSlot;

    /**
     * Слот одежды: тело
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_body", referencedColumnName="id")
     * @var InventoryItem
     */
    private $bodySlot;

    /**
     * Слот одежды: плащ
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_cloak", referencedColumnName="id")
     * @var InventoryItem
     */
    private $cloakSlot;

    /**
     * Слот одежды: оружие или инструмент
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_weapon", referencedColumnName="id")
     * @var InventoryItem
     */
    private $weaponSlot;

    /**
     * Слот одежды: в левой руке
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_left_hand", referencedColumnName="id")
     * @var InventoryItem
     */
    private $leftHandSlot;

    /**
     * Слот одежды: перчатки
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_gloves", referencedColumnName="id")
     * @var InventoryItem
     */
    private $glovesSlot;

    /**
     * Слот одежды: первое кольцо
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_ring_first", referencedColumnName="id")
     * @var InventoryItem
     */
    private $ringFirstSlot;

    /**
     * Слот одежды: второе кольцо
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_ring_second", referencedColumnName="id")
     * @var InventoryItem
     */
    private $ringSecondSlot;

    /**
     * Слот одежды: ноги
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_legs", referencedColumnName="id")
     * @var InventoryItem
     */
    private $legsSlot;

    /**
     * Слот одежды: обувь
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\InventoryItem")
     * @ORM\JoinColumn(name="slot_boots", referencedColumnName="id")
     * @var InventoryItem
     */
    private $bootsSlot;

    /**
     * Дата регистрации
     * @ORM\Column(name="register_date", type="datetime")
     * @var \DateTime
     */
    private $registerDate;

    /**
     * Изображение персонажа (аватар)
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     * @var string
     */
    private $avatar;

    public function __construct() {
        parent::__construct();

        $this->registerDate = new \DateTime();
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
     * @return InventoryItem
     */
    public function getHeadSlot() {
        return $this->headSlot;
    }

    /**
     * @param InventoryItem $headSlot
     */
    public function setHeadSlot($headSlot) {
        $this->headSlot = $headSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getAmuletSlot() {
        return $this->amuletSlot;
    }

    /**
     * @param InventoryItem $amuletSlot
     */
    public function setAmuletSlot($amuletSlot) {
        $this->amuletSlot = $amuletSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getBodySlot() {
        return $this->bodySlot;
    }

    /**
     * @param InventoryItem $bodySlot
     */
    public function setBodySlot($bodySlot) {
        $this->bodySlot = $bodySlot;
    }

    /**
     * @return InventoryItem
     */
    public function getCloakSlot() {
        return $this->cloakSlot;
    }

    /**
     * @param InventoryItem $cloakSlot
     */
    public function setCloakSlot($cloakSlot) {
        $this->cloakSlot = $cloakSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getWeaponSlot() {
        return $this->weaponSlot;
    }

    /**
     * @param InventoryItem $weaponSlot
     */
    public function setWeaponSlot($weaponSlot) {
        $this->weaponSlot = $weaponSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getLeftHandSlot() {
        return $this->leftHandSlot;
    }

    /**
     * @param InventoryItem $leftHandSlot
     */
    public function setLeftHandSlot($leftHandSlot) {
        $this->leftHandSlot = $leftHandSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getGlovesSlot() {
        return $this->glovesSlot;
    }

    /**
     * @param InventoryItem $glovesSlot
     */
    public function setGlovesSlot($glovesSlot) {
        $this->glovesSlot = $glovesSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getRingFirstSlot() {
        return $this->ringFirstSlot;
    }

    /**
     * @param InventoryItem $ringFirstSlot
     */
    public function setRingFirstSlot($ringFirstSlot) {
        $this->ringFirstSlot = $ringFirstSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getRingSecondSlot() {
        return $this->ringSecondSlot;
    }

    /**
     * @param InventoryItem $ringSecondSlot
     */
    public function setRingSecondSlot($ringSecondSlot) {
        $this->ringSecondSlot = $ringSecondSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getLegsSlot() {
        return $this->legsSlot;
    }

    /**
     * @param InventoryItem $legsSlot
     */
    public function setLegsSlot($legsSlot) {
        $this->legsSlot = $legsSlot;
    }

    /**
     * @return InventoryItem
     */
    public function getBootsSlot() {
        return $this->bootsSlot;
    }

    /**
     * @param InventoryItem $bootsSlot
     */
    public function setBootsSlot($bootsSlot) {
        $this->bootsSlot = $bootsSlot;
    }

    /**
     * @return \DateTime
     */
    public function getRegisterDate() {
        return $this->registerDate;
    }

    /**
     * @return string
     */
    public function getAvatar() {
        return $this->avatar ? sprintf('%s%s.%s', self::AVATAR_PATH, $this->avatar, self::AVATAR_EXTENSION) : '';
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }

    /**
     * Одетые на персонаже вещи
     * @return InventoryItem[]
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

    /**
     * @Assert\GreaterThanOrEqual(value = 4, message = "Минимальная длина имени - 4 буквы")
     * @Assert\LessThanOrEqual(value = 20, message = "Максимальная длина имени - 20 букв")
     * @return int
     */
    public function isNameValid() {
        return mb_strlen($this->getLiteralUsername(), 'UTF-8');
    }

    /**
     * Очистка логина от спецсимволов для генерации имени
     * @return string
     */
    public function getLiteralUsername() {
        return preg_replace('/[^a-zA-Zа-яА-Я]/us', '', $this->getUsernameCanonical());
    }
}
