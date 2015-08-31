<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Command\Console\Integration\Truncate;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;
use Rottenwood\KingdomBundle\Entity\Room;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeMapCommand extends Truncate {

    protected function configure() {
        $this->setName('kingdom:purge:map')->setDescription('Удаление всех комнат');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('Удаление комнат ... ');
        $this->truncateEntity(Room::class);
        $output->writeln('Комнаты удалены.');

        $output->write('Удаление типов комнат ... ');
        $this->truncateEntity(RoomType::class);
        $output->writeln('Типы комнаты удалены.');
    }
}
