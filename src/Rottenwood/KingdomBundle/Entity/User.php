<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Игровой персонаж
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\UserRepository")
 */
class User extends BaseUser
{

    const AVATAR_PATH = '/img/avatars/';
    const AVATAR_EXTENSION = 'jpg';
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

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
     * Дата регистрации
     * @ORM\Column(name="register_date", type="datetime")
     * @var \DateTime
     */
    private $registerDate;

    /**
     * Пол персонажа
     * @ORM\Column(name="gender", type="string", length=6, nullable=false)
     * @var string
     */
    private $gender = self::GENDER_MALE;

    /**
     * Изображение персонажа (аватар)
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     * @var string
     */
    private $avatar;

    /**
     * @ORM\Column(name="last_action_waitstate", type="integer", nullable=true)
     * @var int
     */
    private $waitstate;

    public function __construct()
    {
        parent::__construct();

        $this->registerDate = new \DateTime();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

    /**
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar ? sprintf('%s%s.%s', self::AVATAR_PATH, $this->avatar, self::AVATAR_EXTENSION) : '';
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return bool
     */
    public function isMale()
    {
        return $this->gender == self::GENDER_MALE;
    }

    /**
     * @return bool
     */
    public function isFemale()
    {
        return $this->gender == self::GENDER_FEMALE;
    }

    /**
     * @return int
     */
    public function getWaitstate()
    {
        return $this->waitstate;
    }

    /**
     * @param int $waitSeconds
     */
    public function addWaitstate($waitSeconds)
    {
        if (!is_int($waitSeconds)) {
        	throw new \RuntimeException('Waitstate can only accept integer as parameter');
        }

        $this->waitstate = time() + $waitSeconds;
    }

    /**
     * @return bool
     */
    public function isBusy()
    {
        return $this->waitstate > time();
    }

    /**
     * @Assert\GreaterThanOrEqual(value = 4, message = "Минимальная длина имени - 4 буквы")
     * @Assert\LessThanOrEqual(value = 20, message = "Максимальная длина имени - 20 букв")
     * @return int
     */
    public function isNameValid()
    {
        return mb_strlen($this->getLiteralUsername(), 'UTF-8');
    }

    /**
     * Очистка логина от спецсимволов для генерации имени
     * @return string
     */
    public function getLiteralUsername()
    {
        return preg_replace('/[^a-zA-Zа-яА-Я]/us', '', $this->getUsernameCanonical());
    }
}
