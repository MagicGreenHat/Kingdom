<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Command\Console\Integration\Truncate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ItemsPurgeCommand extends Truncate {

    protected function configure() {
        $this->setName('kingdom:items:purge')->setDescription('Удаление всех предметов');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('Удаление предметов в инвентарях ... ');
        $this->truncateEntity('Rottenwood\KingdomBundle\Entity\InventoryItem');
        $output->writeln('Инвентари удалены.');

        $output->write('Удаление предметов ... ');
        $this->truncateEntity('Rottenwood\KingdomBundle\Entity\Infrastructure\Item');
        $output->writeln('Предметы удалены.');
    }
}
