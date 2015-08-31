<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomTypes\Forest;
use Rottenwood\KingdomBundle\Entity\RoomTypes\River;
use Rottenwood\KingdomBundle\Entity\RoomTypes\Road;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMapCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:create:map')->setDescription('Создание карты мира');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();

        $repository = $container->get('kingdom.room_repository');

        $rooms = $repository->findAll();

        if (count($rooms)) {
            $output->writeln(sprintf('Уже создано %d комнат. Удалите их командой kingdom:purge:map', count($rooms)));
        } else {
            $output->write('Создание новых типов комнат ... ');

            $types = [
                new Forest(),
                new River(),
                new Road(),
            ];

            foreach ($types as $type) {
                $repository->persist($type);
            }

            $output->write('Создание новых комнат ... ');

            for ($y = -3; $y <= 3; $y++) {
                for ($x = -3; $x <= 3; $x++) {
                    $room = new Room($x, $y, $types[array_rand($types)]);
                    $repository->persist($room);
                }
            }

            $repository->flush();

            $output->writeln(sprintf('Создано %d новых комнат.', count($repository->findAll())));
        }
    }
}
