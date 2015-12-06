<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Infrastructure\AbstractRepository;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;
use Rottenwood\KingdomBundle\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class CreateMapCommand extends ContainerAwareCommand {

    /** {@inheritdoc} */
    protected function configure() {
        $this->setName('kingdom:create:map')->setDescription('Создание карты мира');
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $repository = $container->get('kingdom.room_repository');
        $rooms = $repository->findAll();

        if (count($rooms)) {
            $output->writeln(sprintf('Уже создано %d комнат. Удалите их командой kingdom:purge:map', count($rooms)));
        } else {
            $roomTypes = $this->createRoomTypes($repository, $output);
            $this->createRooms($repository, $roomTypes, $output);

            $repository->flush();
        }
    }

    /**
     * @param AbstractRepository $repository
     * @param OutputInterface    $output
     * @return RoomType[]
     */
    private function createRoomTypes(AbstractRepository $repository, OutputInterface $output): array {
        /** @var SplFileInfo[] $typeClasses */
        $typeClasses = (new Finder())->files()->name('*.php')->in(__DIR__ . '/../../Entity/RoomTypes');
        $typeNamespace = 'Rottenwood\\KingdomBundle\\Entity\\RoomTypes\\';

        $output->write('Создание типов комнат ... ');

        $roomTypes = [];
        foreach ($typeClasses as $typeFile) {
            $typeName = $typeFile->getBasename('.php');
            $typeClass = $typeNamespace . $typeName;
            $roomType = new $typeClass();
            $roomTypes[strtolower($typeName)] = $roomType;
            $repository->persist($roomType);
        }

        $output->writeln(sprintf('Создано %d новых типов комнат.', count($roomTypes)));

        return $roomTypes;
    }

    /**
     * @param AbstractRepository $repository
     * @param RoomType[]         $roomTypes
     * @param OutputInterface    $output
     * @return Room[]
     */
    private function createRooms(AbstractRepository $repository, array $roomTypes, OutputInterface $output): array {
        $yamlParser = new Yaml();
        $roomsData = $yamlParser->parse(__DIR__ . '/../../Resources/rooms/rooms.yml');

        $output->write('Создание новых комнат ... ');

        $newRooms = [];
        foreach ($roomsData as $roomData) {
            $room = $room = new Room($roomData['x'], $roomData['y'], $roomTypes[$roomData['type']]);
            $newRooms[] = $room;
            $repository->persist($room);
        }

        $output->writeln(sprintf('Создано %d новых комнат.', count($newRooms)));
        
        return $newRooms;
    }
}
