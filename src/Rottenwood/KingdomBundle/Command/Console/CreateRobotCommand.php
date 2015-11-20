<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Robot;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Exception\RoomNotFound;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRobotCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('kingdom:create:robot');
        $this->setDescription('Создание робота');

        $this->addArgument('name',
            InputArgument::REQUIRED,
            'имя робота'
        );

        $this->addArgument('roomId',
            InputArgument::REQUIRED,
            'id комнаты рождения'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $roomId = $input->getArgument('roomId');

        $output->write('Создание робота ... ');

        $container = $this->getContainer();

        $roomRepository = $container->get('kingdom.room_repository');

        /** @var Room $room */
        $room = $roomRepository->find($roomId);

        if (!$room) {
            throw new RoomNotFound();
        }

        $em = $roomRepository->getEntityManager();
        $userService = $container->get('kingdom.user_service');

        $cyrillicName = $userService->transliterate($name);

        $robot = new Robot($cyrillicName);
        $robot->setRoom($room);

        $em->persist($robot);
        $em->flush($robot);
    }
}
