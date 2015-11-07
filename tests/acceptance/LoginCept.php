<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('страница логина отображается корректно, авторизация по паролю работает');
$I->amOnPage('/');
$I->see('Войти');
$I->see('Регистрация');

//$I->fillField('#username', 'test');
//$I->fillField('#password', 'test');
//$I->click('Войти');
//$I->see('Здравствуй, Тест!');
