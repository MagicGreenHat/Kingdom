<?php

namespace Rottenwood\KingdomBundle\Entity\Items;

use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\ItemRepository")
 */
class Key extends Item
{

}
