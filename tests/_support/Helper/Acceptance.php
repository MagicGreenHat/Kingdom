<?php
namespace Helper;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * here you can define custom actions
 * all public methods declared in helper class will be available in $I
 * @package Helper
 */
class Acceptance extends \Codeception\Module
{

    public function amLoggedInAs($username)
    {
        $symfonyModule = $this->getModule('Symfony2');
        $container = $symfonyModule->container;

        $userRepository = $container->get('kingdom.user_repository');

        $user = $userRepository->findOneBy(['username' => $username]);

        $firewall = 'main';
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());

        /** @var Session $session */
        $session = $container->get('session');
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $symfonyModule->setCookie($session->getName(), $session->getId());
    }
}
