<?php

namespace Rottenwood\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Rottenwood\KingdomBundle\Entity\Money;
use Rottenwood\KingdomBundle\Entity\Human as User;
use Rottenwood\KingdomBundle\Service\UserService;
use Rottenwood\UserBundle\Loggers\RegistrationLogger;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as FOSURegistrationController;

class RegistrationController extends FOSURegistrationController {

    public function registerAction(Request $request) {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        /** @var User $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $userService = $this->get('kingdom.user_service');

        $form->handleRequest($request);
        $this->updateUserAndForm($user, $form, $userService);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $user->setAvatar($userService->pickAvatar());
            $user->setRoom($userService->getStartRoom());

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('game_page');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(
                FOSUserEvents::REGISTRATION_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            $this->createMoney($user);
            $userService->giveStarterItems($user);

            /** @var RegistrationLogger $logger */
            $logger = $this->container->get('user.logger.registration');
            $logger->logRegistration($user);

            return $response;
        }

        return $this->render(
            'FOSUserBundle:Registration:register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    private function updateUserAndForm(User &$user, FormInterface &$form, UserService $userService) {
        $cyrillicName = $userService->transliterate($user->getLiteralUsername());

        if ($this->isAllreadyExists($cyrillicName)) {
            $errorMessage = $this->get('translator')->trans('fos_user.username.already_used', [], 'validators');
            $form->addError(new FormError($errorMessage));
        } else {
            $user->setName($cyrillicName);
        }
    }

    /**
     * Определение уникальности имени персонажа
     * @param string $name
     * @return bool
     */
    private function isAllreadyExists($name) {
        return (bool) ($this->get('kingdom.user_repository')->findByName($name));
    }

    /**
     * Создание денег игрока
     * @param User $user
     */
    private function createMoney(User $user) {
        $money = new Money($user);
        $moneyRepository = $this->get('kingdom.money_repository');
        $moneyRepository->persist($money);
        $moneyRepository->flush($money);
    }
}
