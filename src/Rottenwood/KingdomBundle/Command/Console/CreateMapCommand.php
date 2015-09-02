<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
            $output->write('Создание типов комнат ... ');

            $fileFinder = new Finder();

            /** @var SplFileInfo[] $typeClasses */
            $typeClasses = $fileFinder->files()->name('*.php')->in(__DIR__ . '/../../Entity/RoomTypes');

            $typeNamespace = 'Rottenwood\\KingdomBundle\\Entity\\RoomTypes\\';

            $roomTypes = [];
            foreach ($typeClasses as $typeFile) {
                $typeClass = $typeNamespace . $typeFile->getBasename('.php');
                $roomType = new $typeClass();
                $roomTypes[] = $roomType;
                $repository->persist($roomType);
            }

            $output->write('Создание новых комнат ... ');

            for ($y = -3; $y <= 3; $y++) {
                for ($x = -3; $x <= 3; $x++) {
                    $room = new Room($x, $y, $roomTypes[array_rand($roomTypes)]);
                    $repository->persist($room);
                }
            }

            $repository->flush();

            $output->writeln(sprintf('Создано %d новых комнат.', count($repository->findAll())));
        }
    }
}
