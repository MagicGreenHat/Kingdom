<?php

namespace Rottenwood\KingdomBundle\Entity\Items;

use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="item_resourses")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\ItemRepository")
 */
class Resource extends Item {

}
