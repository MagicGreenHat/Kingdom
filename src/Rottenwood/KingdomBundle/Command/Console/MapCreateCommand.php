<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MapCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('kingdom:map:create')->setDescription('Создание карты мира');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var RoomRepository $roomRepository */
        $roomRepository = $em->getRepository(Room::class);

        $rooms = $roomRepository->findAll();

        if (count($rooms)) {
            $output->writeln(sprintf('Уже создано %d комнат. Удалите их командой kingdom:map:purge', count($rooms)));
        } else {
            $output->writeln('Создание новых комнат ...');

            $y = -3;
            for ($x = -3; $x <= 3; $x++) {
                $room = new Room('Лес', $x, $y);
                $em->persist($room);
                $y++;
            }

            $em->flush();

            $output->writeln(sprintf('Создано %d новых комнат.', count($roomRepository->findAll())));
        }
    }
}
