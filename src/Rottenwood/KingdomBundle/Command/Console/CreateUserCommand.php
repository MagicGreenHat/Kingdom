<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Infrastructure\UserRepository;
use Rottenwood\KingdomBundle\Entity\Money;
use Rottenwood\KingdomBundle\Entity\Human as User;
use Rottenwood\UserBundle\Loggers\RegistrationLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateUserCommand extends ContainerAwareCommand
{

    protected function configure()
    {
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

        $this->addArgument('gender',
            InputArgument::OPTIONAL,
            'gender'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');
        $gender = $input->getArgument('gender');

        $output->write('Создание персонажа ... ');

        $container = $this->getContainer();

        $userManager = $container->get('fos_user.user_manager');
        $userRepository = $container->get('kingdom.user_repository');
        $userService = $container->get('kingdom.user_service');

        /** @var User $user */
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $userManager->updateCanonicalFields($user);

        $cyrillicName = $userService->transliterate($user->getUsernameCanonical());

        if ($this->checkUserExist($userRepository, $username, $cyrillicName, $email)) {
            $output->writeln('персонаж уже существует.');
        } else {
            $user->setPlainPassword($password);
            $userManager->updatePassword($user);

            $user->setEnabled(true);
            $user->setAvatar($userService->pickAvatar());
            $user->setName($cyrillicName);
            $user->setRoom($userService->getStartRoom());

            // мужской пол по умолчанию
            if ($gender === User::GENDER_FEMALE) {
                $user->setGender(User::GENDER_FEMALE);
            }

            $this->createMoney($user, $container);

            $userRepository->persist($user);
            $userRepository->flush($user);

            $output->writeln('персонаж создан!');

            $output->writeln('====================================');
            $output->writeln('Логин: ' . $username);
            $output->writeln('Пароль: ' . $password);
            $output->writeln('E-mail: ' . $email);
            $output->writeln('====================================');

            /** @var RegistrationLogger $logger */
            $logger = $container->get('user.logger.registration');

            $logger->logRegistration($user);

        }
    }

    /**
     * Создание денег игрока
     * @param User               $user
     * @param ContainerInterface $container
     */
    private function createMoney(User $user, ContainerInterface $container)
    {
        $money = new Money($user);
        $moneyRepository = $container->get('kingdom.money_repository');
        $moneyRepository->persist($money);
    }

    /**
     * @param UserRepository $userRepository
     * @param string         $username
     * @param string         $cyrillicName
     * @param string         $email
     * @return bool
     */
    private function checkUserExist(UserRepository $userRepository, $username, $cyrillicName, $email)
    {
        $user = $userRepository->findByUsername($username);

        if (!$user) {
            $user = $userRepository->findByName($cyrillicName);
        }

        if (!$user) {
            $user = $userRepository->findByEmail($email);
        }

        return (bool)$user;
    }
}
