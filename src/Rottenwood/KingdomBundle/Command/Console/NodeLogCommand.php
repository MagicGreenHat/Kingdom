<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Exception\InvalidCommandParameter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NodeLogCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:node:log');
        $this->setDescription('Точка входа для логирования событий из node.js');

        $this->addArgument('event',
            InputArgument::REQUIRED,
            'имя события'
        );

        $this->addArgument('userId',
            InputArgument::REQUIRED,
            'id игрока'
        );

        $this->addArgument('userName',
            InputArgument::REQUIRED,
            'имя игрока'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $logger = $this->getContainer()->get('kingdom.logger');

        $event = $input->getArgument('event');
        $userId = $input->getArgument('userId');
        $userName = $input->getArgument('userName');

        if ($event == 'playerEnter') {
            $eventPhrase = 'присоединился к игре';
        } elseif ($event == 'playerExit') {
            $eventPhrase = 'вышел из игры';
        } else {
            throw new InvalidCommandParameter('Неизвестное событие: ' . $event);
        }

        $logger->info(sprintf('[%d]%s %s', $userId, $userName, $eventPhrase));
    }
}
