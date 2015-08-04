<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomType;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MapCreateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:map:create')->setDescription('Создание карты мира');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();

        $repository = $container->get('kingdom.room_repository');

        $rooms = $repository->findAll();

        if (count($rooms)) {
            $output->writeln(sprintf('Уже создано %d комнат. Удалите их командой kingdom:map:purge', count($rooms)));
        } else {
            $output->write('Создание новых типов комнат ... ');

            $types = [
                new RoomType('Лес', 'Густой лес раскинул ветви деревьев.', 'forest'),
                new RoomType('Река',
                    'Широкое мелкое русло с мутной водой заросло травой и водорослями.
                    То и дело на пути попадаются небольшие, но заросшие непроходимым
                    лесом острова. Гигантские деревья нависают над головой, погружая
                    реку в полумрак.',
                    'river'),
                new RoomType('Дорога', 'Пыльная лесная дорога проходит тут.', 'road'),
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
