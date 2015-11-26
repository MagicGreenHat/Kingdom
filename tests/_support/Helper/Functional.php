<?php
namespace Helper;

use Codeception\Module\Symfony2;
use PHPUnit_Framework_Assert;
use Rottenwood\KingdomBundle\Entity\Human;
use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\Room;

/**
 * Методы для функционального тестирования
 */
class Functional extends AbstractHelper
{

    /**
     * Запуск консольной symfony команды
     * @param string $command
     * @param bool   $failNonZero
     * @return string
     */
    public function runSymfonyCommand($command, $failNonZero = true)
    {
        $command = sprintf('/kingdom/app/console %s -e test', $command);

        $data = [];
        exec("$command", $data, $resultCode);
        $output = implode('\n', $data);
        if ($output === null) {
            \PHPUnit_Framework_Assert::fail("$command can't be executed");
        }
        if ($resultCode !== 0 && $failNonZero) {
            \PHPUnit_Framework_Assert::fail("Result code was $resultCode.\n\n" . $output);
        }
        $this->debug(preg_replace('~s/\e\[\d+(?>(;\d+)*)m//g~', '', $output));

        return $output;
    }

    /**
     * Запуск внутриигровой консольной команды и получение ее результата
     * Тестируемые команды выполняются с помощью вебсокет-запросов
     * @param string $command
     * @return string
     */
    public function runCommand($command)
    {
        $command = sprintf('kingdom:execute 1 %s', $command);
        $result = $this->runSymfonyCommand($command);

        return json_decode($result, true);
    }

    /**
     * Проверка наличия предмета в инвентаре
     * @param string $itemId
     * @return InventoryItem
     */
    public function haveItem($itemId)
    {
        $symfonyModule = $this->getSymfonyModule();
        $user = $this->getUser($symfonyModule);

        $itemRepository = $symfonyModule->container->get('kingdom.inventory_item_repository');

        $item = $itemRepository->findOneByUserAndItemId($user, $itemId);

        PHPUnit_Framework_Assert::assertNotNull($item);

        return $item;
    }

    /**
     * Количество денег у персонажа
     * @param int $gold
     * @param int $silver
     * @throws \Codeception\Exception\ModuleException
     */
    public function setMoney($gold = 0, $silver = 0)
    {
        $symfonyModule = $this->getSymfonyModule();
        $user = $this->getUser($symfonyModule);

        $moneyRepository = $symfonyModule->container->get('kingdom.money_repository');
        $money = $moneyRepository->findOneByUser($user);

        $money->setGold($gold);
        $money->setSilver($silver);

        $moneyRepository->flush();
    }

    /**
     * Перемещение персонажа в комнату по координатам
     * @param int $x
     * @param int $y
     * @param int $z
     */
    public function teleportToCoordinates($x, $y, $z = Room::DEFAULT_Z)
    {
        $symfonyModule = $this->getSymfonyModule();
        $user = $this->getUser($symfonyModule);

        $roomRepository = $symfonyModule->container->get('kingdom.room_repository');
        $room = $roomRepository->findOneByXandY($x, $y);

        $user->setRoom($room);

        $roomRepository->flush();
    }

    /**
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function amAtCoordinates($x, $y)
    {
        $symfonyModule = $this->getSymfonyModule();
        $user = $this->getUser($symfonyModule);
        $roomRepository = $symfonyModule->container->get('kingdom.room_repository');
        $room = $roomRepository->findOneByXandY($x, $y);

        PHPUnit_Framework_Assert::assertEquals($user->getRoom()->getId(), $room->getId());
    }

    /**
     * @param int $gold
     * @param int $silver
     */
    public function haveMoney($gold, $silver)
    {
        $result = $this->runCommand('getMoney');

        PHPUnit_Framework_Assert::assertEquals($gold, $result['data']['gold']);
        PHPUnit_Framework_Assert::assertEquals($silver, $result['data']['silver']);
    }

    /**
     * @return Symfony2
     * @throws \Codeception\Exception\ModuleException
     */
    private function getSymfonyModule()
    {
        return $this->getModule('Symfony2');
    }

    /**
     * @param $symfonyModule
     * @return Human
     */
    private function getUser($symfonyModule)
    {
        return $symfonyModule->container->get('security.token_storage')->getToken()->getUser();
    }
}
