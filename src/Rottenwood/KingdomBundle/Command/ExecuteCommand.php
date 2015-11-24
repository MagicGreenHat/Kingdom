<?php

namespace Rottenwood\KingdomBundle\Command;

use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Command\Infrastructure\GameCommandInterface;
use Rottenwood\KingdomBundle\Entity\Infrastructure\User;
use Rottenwood\KingdomBundle\Exception\CommandNotFound;
use Rottenwood\KingdomBundle\Exception\InvalidCommandResponse;
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

        $this->addArgument('parameters',
            InputArgument::OPTIONAL,
            'аргументы команды'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $userId = $input->getArgument('userId');
        $command = $input->getArgument('externalCommand');
        $parameters = $input->getArgument('parameters');

        $output->writeln($this->executeExternal($userId, $command, $parameters));
    }

    /**
     * Запуск внешней команды
     * @param string $userId      Id игрока запросившего запуск команды
     * @param string $commandName Название команды
     * @param string $parameters  Параметры команды
     * @return string json
     */
    private function executeExternal($userId, $commandName, $parameters) {
        $commandClass = __NAMESPACE__ . '\\Game\\' . ucfirst($commandName);
        $container = $this->getContainer();

        try {
            if (class_exists($commandClass)) {
                $userRepository = $container->get('doctrine')->getRepository(User::class);
                $user = $userRepository->find($userId);

                /** @var GameCommandInterface $command */
                $command = new $commandClass($user, $commandName, $parameters, $container);
            } else {
                throw new CommandNotFound(sprintf('Команда %s не найдена', $commandName));
            }

            $result = $command->execute();

            if (!$result instanceof CommandResponse) {
                throw new InvalidCommandResponse;
            }

            $resultData = $result->getData();
        } catch (\Exception $exception) {
            $logger = $container->get('kingdom.logger.commands_errors');
            $logger->info($exception->getMessage(), ['exception' => $exception]);

            $resultData = [];
        }

        return json_encode($resultData);
    }
}
