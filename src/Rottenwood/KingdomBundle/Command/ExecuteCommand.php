<?php

namespace Rottenwood\KingdomBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecuteCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:execute');
        $this->setDescription('Входная точка игры');

        $this->addArgument('userId',
            InputArgument::REQUIRED,
            'id игрока'
        );

        $this->addArgument('externalCommand',
            InputArgument::REQUIRED,
            'команда для выполнения'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $userId = $input->getArgument('userId');
        $command = $input->getArgument('externalCommand');

        $result = [
            'userId' => $userId,
            'command' => $command,
        ];

        $output->writeln(json_encode($result));
    }
}
