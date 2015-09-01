<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\Items\Armor;
use Rottenwood\KingdomBundle\Entity\Items\ResourceWood;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateItemsCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:create:items')->setDescription('Создание тестовых предметов');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();

        $itemRepository = $container->get('kingdom.item_repository');
        $inventortItemRepository = $container->get('kingdom.inventory_item_repository');
        $userRepository = $container->get('kingdom.user_repository');

        $items = $itemRepository->findAllItems();
        $users = $userRepository->findAllUsers();

        if (count($items)) {
            $output->writeln(
                sprintf('Уже создано %d предметов. Удалите их командой kingdom:purge:items', count($items))
            );
        } else {
            $output->write('Создание предметов ... ');

            $itemsData = [
                ['Модная шляпа тестировщика', 'модную шляпу тестировщика', 'Модная шляпа является опознавательным знаком профессионального тестировщика', [Item::USER_SLOT_HEAD], 'hat1'],
                ['Плащ тестировщика', 'плащ тестировщика', 'Синий плащ является частью униформы тестировщика', [Item::USER_SLOT_CLOAK], 'cloak-blue'],
                ['Амулет тестировщика', 'амулет тестировщика', 'Знак благодарности за помощь в тестировании!', [Item::USER_SLOT_AMULET], 'amulettester'],
                ['Стальной щит', 'стальной щит', 'Крепкий стальной щит', [Item::USER_SLOT_LEFT_HAND], 'shield1'],
                ['Рукавицы тестировщика', 'рукавицы тестировщика', 'Ценный аксессуар, помогающий не запачкать руки', [Item::USER_SLOT_GLOVES], 'gloves-blue'],
                ['Оружие тестировщика', 'оружие тестировщика', 'Убийца насекомых!', [Item::USER_SLOT_WEAPON], 'dagger1'],
                ['Рубаха тестировщика', 'рубаху тестировщика', 'Синяя рубашка униформы тестировщиков', [Item::USER_SLOT_BODY], 'shirt1'],
                ['Ботинки тестировщика', 'ботинки тестировщика', 'Крепкие ботинки - ужас насекомых!', [Item::USER_SLOT_BOOTS], 'boots-blue'],
                ['Штаны тестировщика', 'штаны тестировщика', 'Синие штаны униформы тестировщиков', [Item::USER_SLOT_LEGS], 'legs1'],
                ['Кольцо братства тестировщиков', 'кольцо братства тестировщиков', 'Печатка, позволяющая тестировщикам узнавать друг-друга. Про свойства и секреты этого кольца ходят слухи', [Item::USER_SLOT_RING_FIRST, Item::USER_SLOT_RING_SECOND], 'ring1'],
            ];

            foreach ($itemsData as $itemData) {
                $item = new Armor($itemData[0], $itemData[0], $itemData[0], $itemData[1], $itemData[0], $itemData[0], $itemData[2], $itemData[4], $itemData[3]);
                $itemRepository->persist($item);

                $output->writeln(sprintf('Создан предмет %s!', $item->getName()));

                foreach ($users as $user) {
                    $inventoryItem = new InventoryItem($user, $item);
                    $inventortItemRepository->persist($inventoryItem);

                    $output->writeln(
                        sprintf('Предмет "%s" передан персонажу %s.', $item->getName(), $user->getName())
                    );
                }
            }

            foreach ($users as $user) {
                $resourceItem = new ResourceWood('Древесина', 'Древесина', 'Древесина', 'древесину', 'Древесина', 'Древесина', 'Ценный ресурс для производства деревянных предметов', 'resources/wood');
                $itemRepository->persist($resourceItem);

                $inventoryItem = new InventoryItem($user, $resourceItem);
                $inventortItemRepository->persist($inventoryItem);

                $output->writeln(
                    sprintf('Предмет "%s" передан персонажу %s.', $resourceItem->getName(), $user->getName())
                );
            }

            $itemRepository->flush();

            $output->writeln(sprintf('Создано %d новых предметов.', count($itemRepository->findAll())));
        }
    }
}
