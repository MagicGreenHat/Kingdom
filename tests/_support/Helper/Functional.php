<?php
namespace Helper;

use Codeception\Module\Symfony2;
use PHPUnit_Framework_Assert;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\User;

/**
 * here you can define custom actions
 * all public methods declared in helper class will be available in $I
 * @package Helper
 */
class Functional extends AbstractHelper
{

    /**
     * Запуск консольной команды и получение ее результата
     * @param string $command
     * @param bool   $failNonZero
     * @return string
     */
    public function runCommand($command, $failNonZero = true)
    {
        $command = sprintf('/kingdom/app/console kingdom:execute 1 %s -e test', $command);

        $data = [];
        exec("$command", $data, $resultCode);
        $output = implode('\n', $data);
        if ($output === null) {
            \PHPUnit_Framework_Assert::fail('$command can\'t be executed');
        }
        if ($resultCode !== 0 && $failNonZero) {
            \PHPUnit_Framework_Assert::fail('Result code was $resultCode.\n\n' . $output);
        }
        $this->debug(preg_replace('~s/\e\[\d+(?>(;\d+)*)m//g~', '', $output));


        return json_decode($output, true);
    }

    /**
     * Количество денег у персонажа
     * @param int $gold
     * @param int $silver
     * @throws \Codeception\Exception\ModuleException
     */
    public function haveMoney($gold = 0, $silver = 0)
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
     * @return Symfony2
     * @throws \Codeception\Exception\ModuleException
     */
    private function getSymfonyModule()
    {
        return $this->getModule('Symfony2');
    }

    /**
     * @param $symfonyModule
     * @return User
     */
    private function getUser($symfonyModule)
    {
        return $symfonyModule->container->get('security.token_storage')->getToken()->getUser();
    }
}
