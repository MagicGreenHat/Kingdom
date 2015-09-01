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
        $this->truncateEntity(Room::class, $output, 'Удаление комнат ... ', 'Комнаты удалены.');
        $this->truncateEntity(RoomType::class, $output, 'Удаление типов комнат ... ', 'Типы комнат удалены.');
    }
}
