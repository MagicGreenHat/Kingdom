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

        $result = [];

        if ($command == 'north') {
            $result['mapData'] = [
                'a1' => 1,
                'a2' => 2,
                'a3' => 3,
            ];
        } elseif ($command == 'south') {
            $result['mapData'] = [
                'a1' => 3,
                'a2' => 2,
                'a3' => 1,
            ];
        }

        $output->writeln(json_encode($result));
    }

    /**
     * Запуск внешней команды
     * @param string $userId     id игрока запросившего запуск команды
     * @param string $command    название команды
     * @param array  $attributes параметры команды
     * @return string json
     */
    private function executeExternal($userId, $command, array $attributes) {

        //TODO[Rottenwood]: Поиск класса нужной команды. Если команда не найдена - эксепшен.
        $result = json_encode([]);

        return $result;
    }
}
