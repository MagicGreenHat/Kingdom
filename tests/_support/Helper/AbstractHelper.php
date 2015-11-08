<?php
namespace Helper;

use Codeception\Module\Symfony2;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AbstractHelper extends \Codeception\Module
{

    public function amLoggedInAs($username)
    {
        /** @var Symfony2 $symfonyModule */
        $symfonyModule = $this->getModule('Symfony2');
        $container = $symfonyModule->container;

        $userRepository = $container->get('kingdom.user_repository');

        $user = $userRepository->findOneBy(['username' => $username]);

        $firewall = 'main';
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());

        $container->get('security.token_storage')->setToken($token);

        /** @var Session $session */
        $session = $container->get('session');
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $symfonyModule->setCookie($session->getName(), $session->getId());
    }
}
