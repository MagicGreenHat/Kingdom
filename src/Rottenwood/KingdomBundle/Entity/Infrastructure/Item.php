<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\User;

/**
 * Игровой предмет
 * @ORM\MappedSuperclass
 */
abstract class Item {

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
     * @return string
     */
    public function getPicture() {
        return $this->picture;
    }
}
