<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Command\Console\Integration\Truncate;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\RoomResource;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeItemsCommand extends Truncate {

    protected function configure() {
        $this->setName('kingdom:purge:items')->setDescription('Удаление всех предметов');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->truncateEntity(InventoryItem::class, $output, 'Удаление предметов в инвентарях ... ', 'Инвентари удалены.');
        $this->truncateEntity(Item::class, $output, 'Удаление предметов ... ', 'Предметы удалены.');
        $this->truncateEntity(RoomResource::class, $output, 'Удаление ресурсов ... ', 'Ресурсы удалены.');
    }
}
