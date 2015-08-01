<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\UserRepository")
 */
class User extends BaseUser {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Комната в которой находится персонаж
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(name="room", referencedColumnName="id")
     * @var Room
     */
    private $room;
}
