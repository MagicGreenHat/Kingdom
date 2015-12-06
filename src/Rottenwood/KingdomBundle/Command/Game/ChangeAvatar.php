<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;

/**
 * Смена аватара (изображения игрока)
 * Параметры: string - название изображения без пути и раширения
 * Применение в js: Kingdom.Websocket.command('changeAvatar', 'male2')
 */
class ChangeAvatar extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute(): CommandResponse {
        if ($this->parameters) {
            $this->user->setAvatar($this->parameters);

            $this->result->setData(['avatar' => $this->user->getAvatar()]);
            $this->container->get('doctrine.orm.entity_manager')->flush($this->user);
        }

        return $this->result;
    }
}
