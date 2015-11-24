<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\User;
use Rottenwood\KingdomBundle\Entity\Infrastructure\PlayableCharacter;

/**
 * Робот
 * @ORM\Entity()
 */
class Robot extends User implements PlayableCharacter
{

    public function __construct()
    {
        parent::__construct();

        $username = 'Robot_' . $this->generateRandomLetters();
        $this->setUsername($username);
        $this->setEmail($username . '@' . $this->generateRandomLetters() . '.bot');
        $this->setPlainPassword($this->generateRandomLetters());
    }

    /**
     * Генератор случайных наборов букв
     * @return string
     */
    private function generateRandomLetters()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }
}
