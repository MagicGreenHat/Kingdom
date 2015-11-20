<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\PlayableCharacter;

/**
 * Робот
 * @ORM\Table(name="robots")
 * @ORM\Entity()
 */
class Robot implements PlayableCharacter
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * Русское имя робота
     * @ORM\Column(name="robot_name", type="string", length=25, unique=true)
     * @var string
     */
    protected $name;

    /**
     * Комната в которой находится робот
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(name="room", referencedColumnName="id", nullable=false)
     * @var Room
     */
    protected $room;

    /**
     * Дата регистрации
     * @ORM\Column(name="register_date", type="datetime")
     * @var \DateTime
     */
    protected $registerDate;

    public function __construct($name)
    {
        $this->registerDate = new \DateTime();
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param Room $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }
}
