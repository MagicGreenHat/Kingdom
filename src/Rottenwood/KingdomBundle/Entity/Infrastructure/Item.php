<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Doctrine\ORM\Mapping as ORM;

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
 *      "resource_wood" = "Rottenwood\KingdomBundle\Entity\Items\ResourceWood",
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
     * @var string
     * @ORM\Column(name="id", type="string", length=100)
     * @ORM\Id
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
     * Слоты куда можно одеть предмет
     * @var string[]
     * @ORM\Column(name="slots", type="simple_array", nullable=true)
     */
    protected $slots;

    /**
     * Название изображения предмета
     * @var string
     * @ORM\Column(name="picture", type="string", length=255)
     */
    protected $picture;

    /**
     * @param string   $id
     * @param string   $name
     * @param string   $name2
     * @param string   $name3
     * @param string   $name4
     * @param string   $name5
     * @param string   $name6
     * @param string   $description
     * @param string   $picture
     * @param string[] $slots
     */
    public function __construct($id, $name, $name2, $name3, $name4, $name5, $name6, $description, $picture, array $slots = []) {
        $this->id = $id;
        $this->name = $name;
        $this->name2 = $name2;
        $this->name3 = $name3;
        $this->name4 = $name4;
        $this->name5 = $name5;
        $this->name6 = $name6;
        $this->description = $description;
        $this->picture = $picture;
        $this->slots = $slots;
    }

    /**
     * @return string
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
     * @return string[]
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
