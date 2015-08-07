<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\User;

/**
 * Игровой предмет
 * @ORM\Table(name="items")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\ItemRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      "armor" = "Rottenwood\KingdomBundle\Entity\Items\Armor",
 *      "clothes" = "Rottenwood\KingdomBundle\Entity\Items\Clothes",
 *      "food" = "Rottenwood\KingdomBundle\Entity\Items\Food",
 *      "key" = "Rottenwood\KingdomBundle\Entity\Items\Key",
 *      "resource" = "Rottenwood\KingdomBundle\Entity\Items\Resource",
 *      "ring" = "Rottenwood\KingdomBundle\Entity\Items\Ring",
 *      "scroll" = "Rottenwood\KingdomBundle\Entity\Items\Scroll",
 *      "shield" = "Rottenwood\KingdomBundle\Entity\Items\Shield",
 *      "weapon" = "Rottenwood\KingdomBundle\Entity\Items\Weapon",
 * })
 */
abstract class Item {

    const USER_SLOT_HEAD = 'head';
    const USER_SLOT_AMULET = 'amulet';
    const USER_SLOT_BODY = 'body';
    const USER_SLOT_CLOAK = 'cloak';
    const USER_SLOT_WEAPON = 'weapon';
    const USER_SLOT_LEFT_HAND = 'left_hand';
    const USER_SLOT_GLOVES = 'gloves';
    const USER_SLOT_RING_FIRST = 'ring_first';
    const USER_SLOT_RING_SECOND = 'ring_second';
    const USER_SLOT_LEGS = 'legs';
    const USER_SLOT_BOOTS = 'boots';

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Название в именительном падеже
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * Название в родительном падеже
     * @var string
     * @ORM\Column(name="name_2", type="string", length=255)
     */
    protected $name2;

    /**
     * Название в дательном падеже
     * @var string
     * @ORM\Column(name="name_3", type="string", length=255)
     */
    protected $name3;

    /**
     * Название в винительном падеже
     * @var string
     * @ORM\Column(name="name_4", type="string", length=255)
     */
    protected $name4;

    /**
     * Название в творительном падеже
     * @var string
     * @ORM\Column(name="name_5", type="string", length=255)
     */
    protected $name5;

    /**
     * Название в предложном падеже
     * @var string
     * @ORM\Column(name="name_6", type="string", length=255)
     */
    protected $name6;

    /**
     * Описание предмета
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * Персонаж у которого находится предмет
     * @var User
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Слоты куда можно одеть предмет
     * @var int[]
     * @ORM\Column(name="slots", type="simple_array")
     */
    protected $slots;

    /**
     * Название изображения предмета
     * @var string
     * @ORM\Column(name="picture", type="string", length=255)
     */
    protected $picture;

    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName2() {
        return $this->name2;
    }

    /**
     * @return string
     */
    public function getName3() {
        return $this->name3;
    }

    /**
     * @return string
     */
    public function getName4() {
        return $this->name4;
    }

    /**
     * @return string
     */
    public function getName5() {
        return $this->name5;
    }

    /**
     * @return string
     */
    public function getName6() {
        return $this->name6;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * @return int[]
     */
    public function getSlots() {
        return $this->slots;
    }

    /**
     * @return string
     */
    public function getPicture() {
        return $this->picture;
    }

    /**
     * Является ли предмет оружием
     * @return bool
     */
    public function isWeapon() {
        return $this->fitsTo(self::USER_SLOT_WEAPON);
    }

    /**
     * Подходит ли предмет в соответствующий слот
     * @param int $slotName
     * @return bool
     */
    public function fitsTo($slotName) {
        return in_array($slotName, $this->slots);
    }

    /**
     * Названия всех слотов
     * @return string[]
     */
    public static function getAllSlotNames() {
        return [
            self::USER_SLOT_HEAD,
            self::USER_SLOT_AMULET,
            self::USER_SLOT_BODY,
            self::USER_SLOT_CLOAK,
            self::USER_SLOT_WEAPON,
            self::USER_SLOT_LEFT_HAND,
            self::USER_SLOT_GLOVES,
            self::USER_SLOT_RING_FIRST,
            self::USER_SLOT_RING_SECOND,
            self::USER_SLOT_LEGS,
            self::USER_SLOT_BOOTS,
        ];
    }
}
