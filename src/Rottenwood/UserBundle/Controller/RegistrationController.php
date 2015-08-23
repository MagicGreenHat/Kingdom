<?php

namespace Rottenwood\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Rottenwood\KingdomBundle\Entity\Money;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\User;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
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

        $form->handleRequest($request);
        $this->updateUserAndForm($user, $form);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            /** @var Room $room */
            $room = $this->get('kingdom.room_repository')->findOneByXandY(0, 0);

            $user->setAvatar($this->pickAvatar());
            $user->setRoom($room);

            $this->createMoney();

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('game_page');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(
                FOSUserEvents::REGISTRATION_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $response;
        }

        return $this->render(
            'FOSUserBundle:Registration:register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    private function updateUserAndForm(User &$user, FormInterface &$form) {
        $cyrillicName = $this->transliterate($user->getLiteralUsername());

        if ($this->isAllreadyExists($cyrillicName)) {
            $errorMessage = $this->get('translator')->trans('fos_user.username.already_used', [], 'validators');
        	$form->addError(new FormError($errorMessage));
        } else {
            $user->setName($cyrillicName);
        }
    }

    /**
     * Транслитерация имени
     * @param string $string
     * @return string
     */
    private function transliterate($string) {
        return mb_convert_case(strtr($string, $this->getAlphabet()), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Массив соответствия русских букв латинским
     * @return string[]
     */
    private function getAlphabet() {
        return [
            'a' => 'а',  'b' => 'б', 'c' => 'ц', 'd' => 'д',  'e' => 'е',
            'f' => 'ф',  'g' => 'г', 'h' => 'х', 'i' => 'ай', 'j' => 'дж',
            'k' => 'к',  'l' => 'л', 'm' => 'м', 'n' => 'н',  'o' => 'о',
            'p' => 'п',  'q' => 'к', 'r' => 'р', 's' => 'с',  't' => 'т',
            'u' => 'ю',  'v' => 'в', 'w' => 'в', 'x' => 'кс', 'y' => 'й',
            'z' => 'з',  'A' => 'А', 'B' => 'Б', 'C' => 'Ц',  'D' => 'Д',
            'E' => 'Е',  'F' => 'Ф', 'G' => 'Г', 'H' => 'Х',  'I' => 'Ай',
            'J' => 'Дж', 'K' => 'К', 'L' => 'Л', 'M' => 'М',  'N' => 'Н',
            'O' => 'О',  'P' => 'П', 'Q' => 'К', 'R' => 'Р',  'S' => 'С',
            'T' => 'Т',  'U' => 'Ю', 'V' => 'В', 'W' => 'В',  'X' => 'Кс',
            'Y' => 'Й',  'Z' => 'З',
        ];
    }

    /**
     * Установка рэндомного аватара
     * @return string
     */
    private function pickAvatar() {
        $finder = new Finder();

        $prefix = 'male';
        $avatarPath = $this->get('kernel')->getRootDir() . '/../web/img/avatars/' . $prefix;

        $files = $finder->files()->in($avatarPath);

        $avatars = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $avatars[] = $file->getBasename('.jpg');
        }

        $avatar = $prefix . '/' . $avatars[array_rand($avatars)];

        return $avatar;
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
