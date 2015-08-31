<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Command\Console\Integration\Truncate;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeItemsCommand extends Truncate {

    protected function configure() {
        $this->setName('kingdom:purge:items')->setDescription('Удаление всех предметов');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('Удаление предметов в инвентарях ... ');
        $this->truncateEntity(InventoryItem::class);
        $output->writeln('Инвентари удалены.');

        $output->write('Удаление предметов ... ');
        $this->truncateEntity(Item::class);
        $output->writeln('Предметы удалены.');
    }
}
