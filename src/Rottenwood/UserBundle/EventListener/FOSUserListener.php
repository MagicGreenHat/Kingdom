<?php

namespace Rottenwood\UserBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
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

    /** @var EntityManager */
    private $entityManager;
    /** @var UrlGeneratorInterface */
    private $router;

    /**
     * @param UrlGeneratorInterface $router
     * @param EntityManager         $entityManager
     */
    public function __construct(UrlGeneratorInterface $router, EntityManager $entityManager) {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public static function getSubscribedEvents() {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    public function onRegistrationSuccess(FormEvent $event) {
        $roomRepository = $this->entityManager->getRepository(Room::class);
        /** @var User $user */
        $user = $event->getForm()->getData();
        /** @var Room $room */
        $room = $roomRepository->find(1);

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
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'x',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shh',
            'ь' => '\'',
            'ы' => 'y',
            'ъ' => '\'\'',
            'э' => 'e\'',
            'ю' => 'yu',
            'я' => 'ya',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'YO',
            'Ж' => 'Zh',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'X',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'SHH',
            'Ь' => '\'',
            'Ы' => 'Y\'',
            'Ъ' => '\'\'',
            'Э' => 'E\'',
            'Ю' => 'YU',
            'Я' => 'YA',
        ];

        return strtr($string, array_flip($translit));
    }
}
