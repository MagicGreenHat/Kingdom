<?php

namespace Rottenwood\UserBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomRepository;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Слушатель событий FOSUserBundle
 * @package Rottenwood\BarchartBundle\Listener
 */
class FOSUserListener implements EventSubscriberInterface {

    /** @var UrlGeneratorInterface */
    private $router;
    /** @var RoomRepository */
    private $roomRepository;

    /**
     * @param UrlGeneratorInterface $router
     * @param RoomRepository        $roomRepository
     */
    public function __construct(UrlGeneratorInterface $router, RoomRepository $roomRepository) {
        $this->router = $router;
        $this->roomRepository = $roomRepository;
    }

    public static function getSubscribedEvents() {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    public function onRegistrationSuccess(FormEvent $event) {
        /** @var User $user */
        $user = $event->getForm()->getData();
        /** @var Room $room */
        $room = $this->roomRepository->find(1);

        // Приведение имени к нужному виду
        $newName = explode(' ', $user->getUsernameCanonical())[0];
        $newName = $this->transliterate($newName);
        $newName = preg_replace('/[[:print:]]/', '', $newName);
        $newName = preg_replace('/№/', '', $newName);
        $newName = mb_convert_case($newName, MB_CASE_TITLE, 'UTF-8');

        $user->setUsername($newName);
        $user->setRoom($room);

        $url = $this->router->generate('game_page');
        $event->setResponse(new RedirectResponse($url));
    }

    /**
     * Транслитерация строк
     * @param string $string
     * @return string
     */
    private function transliterate($string) {
        $translit = [
            'a' => 'а',
            'b' => 'б',
            'c' => 'ц',
            'd' => 'д',
            'e' => 'е',
            'f' => 'ф',
            'g' => 'г',
            'h' => 'х',
            'i' => 'ай',
            'j' => 'дж',
            'k' => 'к',
            'l' => 'л',
            'm' => 'м',
            'n' => 'н',
            'o' => 'о',
            'p' => 'п',
            'q' => 'к',
            'r' => 'р',
            's' => 'с',
            't' => 'т',
            'u' => 'ю',
            'v' => 'в',
            'w' => 'в',
            'x' => 'икс',
            'y' => 'й',
            'z' => 'з',
            'A' => 'А',
            'B' => 'Б',
            'C' => 'Ц',
            'D' => 'Д',
            'E' => 'Е',
            'F' => 'Ф',
            'G' => 'Г',
            'H' => 'Х',
            'I' => 'Ай',
            'J' => 'Дж',
            'K' => 'К',
            'L' => 'Л',
            'M' => 'М',
            'N' => 'Н',
            'O' => 'О',
            'P' => 'П',
            'Q' => 'К',
            'R' => 'Р',
            'S' => 'С',
            'T' => 'Т',
            'U' => 'Ю',
            'V' => 'В',
            'W' => 'В',
            'X' => 'Икс',
            'Y' => 'Й',
            'Z' => 'З',
        ];

        return strtr($string, $translit);
    }
}
