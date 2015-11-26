<?php

use Rottenwood\KingdomBundle\Entity\Human;

$I = new FunctionalTester($scenario);
$I->wantTo('Register and see starter items in inventory');

$I->amOnPage('/register');

$I->see('Регистрация в Государстве');

$I->fillField('#fos_user_registration_form_username', 'tester');
$I->fillField('#fos_user_registration_form_email', 'tester@tester.ru');
$I->fillField('#fos_user_registration_form_plainPassword_first', 'tester');
$I->fillField('#fos_user_registration_form_plainPassword_second', 'tester');

$I->click('Регистрация');

$I->seeInRepository(Human::class,
    [
        'name'   => 'Тестер',
        'email'  => 'tester@tester.ru',
        'gender' => 'male',
    ]
);

$I->amLoggedInAs('tester');

$I->haveItem('newbie-boots');
$I->haveItem('newbie-legs');
$I->haveItem('newbie-shirt');
$I->haveItem('tester-sword');
