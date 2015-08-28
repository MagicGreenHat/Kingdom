<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\Items\Armor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ItemsCreateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:items:create')->setDescription('Создание тестовых предметов');
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
                sprintf('Уже создано %d предметов. Удалите их командой kingdom:items:purge', count($items))
            );
        } else {
            $output->write('Создание предметов ... ');

            $itemsData = [
                ['Модная шляпа тестировщика', 'Модная шляпа является опознавательным знаком профессионального тестировщика', [Item::USER_SLOT_HEAD], 'hat1'],
                ['Плащ тестировщика', 'Синий плащ является частью униформы тестировщика', [Item::USER_SLOT_CLOAK], 'cloak-blue'],
                ['Амулет тестировщика', 'Знак благодарности за помощь в тестировании!', [Item::USER_SLOT_AMULET], 'amulettester'],
                ['Стальной щит', 'Крепкий стальной щит', [Item::USER_SLOT_LEFT_HAND], 'shield1'],
                ['Рукавицы тестировщика', 'Ценный аксессуар, помогающий не запачкать руки', [Item::USER_SLOT_GLOVES], 'gloves-blue'],
                ['Оружие тестировщика', 'Убийца насекомых!', [Item::USER_SLOT_WEAPON], 'dagger1'],
                ['Рубаха тестировщика', 'Синяя рубашка униформы тестировщиков', [Item::USER_SLOT_BODY], 'shirt1'],
                ['Ботинки тестировщика', 'Крепкие ботинки - ужас насекомых!', [Item::USER_SLOT_BOOTS], 'boots-blue'],
                ['Штаны тестировщика', 'Синие штаны униформы тестировщиков', [Item::USER_SLOT_LEGS], 'legs1'],
                ['Кольцо братства тестировщиков', 'Печатка, позволяющая тестировщикам узнавать друг-друга. Про свойства и секреты этого кольца ходят слухи', [Item::USER_SLOT_RING_FIRST, Item::USER_SLOT_RING_SECOND], 'ring1'],
            ];

            foreach ($itemsData as $itemData) {
                $item = new Armor($itemData[0], $itemData[0], $itemData[0], $itemData[0], $itemData[0], $itemData[0], $itemData[1], $itemData[3], $itemData[2]);
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

            $itemRepository->flush();

            $output->writeln(sprintf('Создано %d новых предметов.', count($itemRepository->findAll())));
        }
    }
}
