<?php

namespace Rottenwood\KingdomBundle\Command;

use Rottenwood\KingdomBundle\Command\Game\GameCommandInterface;
use Rottenwood\KingdomBundle\Entity\User;
use Rottenwood\KingdomBundle\Entity\UserRepository;
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
        } else {
            $result = $this->executeExternal($userId, $command, []);
        }

        $output->writeln(json_encode($result));
    }

    /**
     * Запуск внешней команды
     * @param string $userId      Id игрока запросившего запуск команды
     * @param string $commandName Название команды
     * @param array  $attributes  Параметры команды
     * @return string json
     * @throws \Exception
     */
    private function executeExternal($userId, $commandName, array $attributes) {
        $commandClass = __NAMESPACE__ . '\\Game\\' . ucfirst($commandName);

        if (class_exists($commandClass)) {
            $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
            /** @var UserRepository $userRepository */
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->findById($userId);

            /** @var GameCommandInterface $command */
            $command = new $commandClass($user, $attributes);
        } else {
            throw new \Exception('Команда не найдена');
        }

        $result = json_encode($command->execute());

        return $result;
    }
}
