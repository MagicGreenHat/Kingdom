<?php

namespace Rottenwood\KingdomBundle\Entity\Items;

use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="item_shields")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\ItemRepository")
 */
class Shield extends Item {

}
