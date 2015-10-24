<?php

namespace Rottenwood\KingdomBundle\Entity;

/**
 * Робот
 * @ORM\Table(name="robots")
 * @ORM\Entity()
 */
class Robot extends User {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;
}
