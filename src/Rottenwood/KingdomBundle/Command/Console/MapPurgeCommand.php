<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Command\Console\Integration\TruncateEntity;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MapPurgeCommand extends TruncateEntity {

    protected function configure() {
        $this->setName('kingdom:map:purge')->setDescription('Удаление всех комнат');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('Удаление комнат ... ');
        $this->truncateEntity('Rottenwood\KingdomBundle\Entity\Room');
        $output->writeln('Комнаты удалены.');

        $output->write('Удаление типов комнат ... ');
        $this->truncateEntity('Rottenwood\KingdomBundle\Entity\RoomType');
        $output->writeln('Типы комнаты удалены.');
    }
}
