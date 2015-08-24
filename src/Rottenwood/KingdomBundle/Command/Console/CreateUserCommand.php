<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Money;
use Rottenwood\KingdomBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateUserCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:create:user');
        $this->setDescription('Создание персонажа');

        $this->addArgument('username',
            InputArgument::REQUIRED,
            'имя игрока'
        );

        $this->addArgument('password',
            InputArgument::REQUIRED,
            'пароль'
        );

        $this->addArgument('email',
            InputArgument::REQUIRED,
            'e-mail'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        $container = $this->getContainer();

        $userManager = $container->get('fos_user.user_manager');
        $userRepository = $container->get('kingdom.user_repository');
        $userService = $container->get('kingdom.user_service');

        /** @var User $user */
        $user = $userManager->createUser();

        $user->setUsername($username);
        $user->setPlainPassword($password);
        $userManager->updateCanonicalFields($user);
        $userManager->updatePassword($user);

        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setAvatar($userService->pickAvatar());
        $user->setName($userService->transliterate($user->getUsernameCanonical()));
        $user->setRoom($userService->getStartRoom());

        $this->createMoney($user, $container);

        $userRepository->persist($user);
        $userRepository->flush($user);
    }

    /**
     * Создание денег игрока
     * @param User               $user
     * @param ContainerInterface $container
     */
    private function createMoney(User $user, ContainerInterface $container) {
        $money = new Money($user);
        $moneyRepository = $container->get('kingdom.money_repository');
        $moneyRepository->persist($money);
    }
}
