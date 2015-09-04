<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\Items\ResourceWood;
use Rottenwood\KingdomBundle\Entity\RoomResource;
use Rottenwood\KingdomBundle\Entity\RoomTypes\Forest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Parser;

class CreateItemsCommand extends ContainerAwareCommand {

    const RESOURCE_QUANTITY = 10;

    protected function configure() {
        $this->setName('kingdom:create:items')->setDescription('Создание тестовых предметов');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();

        $itemRepository = $container->get('kingdom.item_repository');
        $userRepository = $container->get('kingdom.user_repository');
        $em = $itemRepository->getEntityManager();

        $items = $itemRepository->findAllItems();
        $users = $userRepository->findAllUsers();
        $rooms = $container->get('kingdom.room_repository')->findAllRooms();

        if (count($items)) {
            $output->writeln(
                sprintf('Уже создано %d предметов. Удалите их командой kingdom:purge:items', count($items))
            );
        } else {
            $output->writeln('Загрузка данных о предметах ... ');
            $allNewItemsData = $this->parseItemsFromYaml();

            foreach ($allNewItemsData as $newItemType => $newItemsData) {
                $newItemType = mb_convert_case($newItemType, MB_CASE_TITLE);
                $itemClass = 'Rottenwood\\KingdomBundle\\Entity\\Items\\' . $newItemType;

                foreach ($newItemsData as $newItemId => $newItemData) {
                    /** @var Item $newItem */
                    $newItem = new $itemClass(
                        $newItemId,
                        $newItemData['name'][0],
                        $newItemData['name'][1],
                        $newItemData['name'][2],
                        $newItemData['name'][3],
                        $newItemData['name'][4],
                        $newItemData['name'][5],
                        $newItemData['desc'],
                        $newItemData['pic'],
                        is_array($newItemData['slots']) ? $newItemData['slots'] : [$newItemData['slots']]
                    );

                    $em->persist($newItem);

                    $output->writeln(sprintf('Создан предмет %s!', $newItem->getName()));

                    foreach ($users as $user) {
                        $inventoryItem = new InventoryItem($user, $newItem);
                        $em->persist($inventoryItem);

                        $output->writeln(
                            sprintf('Предмет "%s" передан персонажу %s.', $newItem->getName(), $user->getName())
                        );
                    }

                    if ($newItem instanceof ResourceWood) {
                        foreach ($rooms as $room) {
                            if ($room->getType() instanceof Forest) {
                                $roomResource = new RoomResource($room, $newItem, self::RESOURCE_QUANTITY);
                                $em->persist($roomResource);

                                $output->writeln(
                                    sprintf('Ресурс "%s" добавлен в комнату %s[%d/%d] в количестве %d единиц.', $newItem->getName(), $room->getName(), $room->getX(), $room->getY(), $roomResource->getQuantity())
                                );
                            }
                        }
                    }
                }
            }

            $em->flush();

            $output->writeln('Создано новых предметов: ' . count($itemRepository->findAll()));
        }
    }

    /**
     * Парсинг yaml-файлов с данными об игровых предметах
     * @return array
     */
    private function parseItemsFromYaml() {
        $yamlParser = new Parser();
        $fileFinder = new Finder();

        /** @var SplFileInfo[] $yamlFiles */
        $yamlFiles = $fileFinder->files()->in(__DIR__ . '/../../Resources/items')->name('*.yml');

        $yamlData = [];
        foreach ($yamlFiles as $yamlFile) {
            $yamlData = array_merge($yamlParser->parse(file_get_contents($yamlFile->getPathname())), $yamlData);
        }

        return $yamlData;
    }
}
