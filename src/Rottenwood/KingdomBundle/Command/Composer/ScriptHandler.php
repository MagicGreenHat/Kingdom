<?php

namespace Rottenwood\KingdomBundle\Command\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as ComposerScriptHandler;
use Composer\Script\CommandEvent; // Не ошибка. IDE не воспринимает строку как валидный класс

class ScriptHandler extends ComposerScriptHandler {

    /**
     * @param $event CommandEvent
     */
    public static function configureKingdom(CommandEvent $event) {
        $consoleDir = self::getConsoleDir($event, 'configure Kingdom');

        // Вызов symfony-команд для настройки государства
        static::executeCommand($event, $consoleDir, 'kingdom:map:create');
        static::executeCommand($event, $consoleDir, 'kingdom:items:create');
    }
}
