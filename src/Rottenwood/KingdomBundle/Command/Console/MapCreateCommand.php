<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomRepository;
use Rottenwood\KingdomBundle\Entity\RoomType;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MapCreateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:map:create')->setDescription('Создание карты мира');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var RoomRepository $roomRepository */
        $roomRepository = $em->getRepository(Room::class);

        $rooms = $roomRepository->findAll();

        if (count($rooms)) {
            $output->writeln(sprintf('Уже создано %d комнат. Удалите их командой kingdom:map:purge', count($rooms)));
        } else {
            $output->write('Создание новых типов комнат ... ');

            $types = [
                new RoomType('Лес', 'forest'),
                new RoomType('Река', 'river'),
                new RoomType('Дорога', 'road'),
            ];

            foreach ($types as $type) {
                $em->persist($type);
            }

            $output->write('Создание новых комнат ... ');

            for ($y = -3; $y <= 3; $y++) {
                for ($x = -3; $x <= 3; $x++) {
                    $room = new Room($x, $y, $types[array_rand($types)]);
                    $em->persist($room);
                }
            }

            $em->flush();

            $output->writeln(sprintf('Создано %d новых комнат.', count($roomRepository->findAll())));
        }
    }
}
