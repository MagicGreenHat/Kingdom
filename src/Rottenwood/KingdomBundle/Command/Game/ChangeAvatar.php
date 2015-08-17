<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;

/**
 * Смена аватара (изображения игрока)
 * Параметры: string - название изображения без пути и раширения
 * Пример: callCommand('changeAvatar', 'male2')
 */
class ChangeAvatar extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $result = new CommandResponse('changeAvatar');

        if ($this->parameters) {
            $this->user->setAvatar($this->parameters);

            $result->setData(['avatar' => $this->user->getAvatar()]);
            $this->container->get('doctrine.orm.entity_manager')->flush($this->user);
        }

        return $result;
    }
}
