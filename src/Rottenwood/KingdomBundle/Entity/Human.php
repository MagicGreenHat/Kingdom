<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\PlayableCharacter;
use Rottenwood\KingdomBundle\Entity\Infrastructure\User;

/**
 * Игровой персонаж
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\HumanRepository")
 */
class Human extends User implements PlayableCharacter
{
}
