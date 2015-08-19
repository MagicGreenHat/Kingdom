<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;

/**
 * Запрос количества денег у персонажа
 * Применение в js: callCommand('getMoney')
 */
class GetMoney extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $result = new CommandResponse('getMoney');

        $moneyRepository = $this->container->get('kingdom.money_repository');
        $money = $moneyRepository->findOneByUser($this->user);

        if ($money) {
            $result->setData(
                [
                    'gold'   => $money->getGold(),
                    'silver' => $money->getSilver(),
                ]
            );
        }


        return $result;
    }
}
