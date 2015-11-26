<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Register in game');

$I->amOnPage('/');

$I->see('Войти');
$I->see('Регистрация');

$I->click('Регистрация');

$I->see('Регистрация в Государстве');

$I->fillField('#fos_user_registration_form_username', 'tester');
$I->fillField('#fos_user_registration_form_email', 'tester@tester.ru');
$I->fillField('#fos_user_registration_form_plainPassword_first', 'tester');
$I->fillField('#fos_user_registration_form_plainPassword_second', 'tester');

$I->click('Регистрация');

$I->see('Здравствуй, Тестер!');
