<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип локации
 * @ORM\Table(name="room_types", uniqueConstraints={@ORM\UniqueConstraint(name="index_unique_name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      "forest" = "Rottenwood\KingdomBundle\Entity\RoomTypes\Forest",
 *      "river" = "Rottenwood\KingdomBundle\Entity\RoomTypes\River",
 *      "road" = "Rottenwood\KingdomBundle\Entity\RoomTypes\Road",
 * })
 */
abstract class RoomType {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * Название
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @var string
     */
    protected $name;

    /**
     * Описание
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    protected $description;

    /**
     * Имя изображения на карте
     * @ORM\Column(name="picture", type="string", length=255)
     * @var string
     */
    protected $picture;

    /**
     * @param string $name
     * @param string $description
     * @param string $picture
     */
    public function __construct($name = '', $description = '', $picture = '') {
        if ($name) {
            $this->name = $name;
        }

        if ($description) {
            $this->description = $description;
        }

        if ($picture) {
            $this->picture = $picture;
        }
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPicture() {
        return $this->picture;
    }
}
