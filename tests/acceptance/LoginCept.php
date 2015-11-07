<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Login page renders correctly, authorization by login as test user');
$I->amOnPage('/');
$I->see('Войти');
$I->see('Регистрация');

$I->fillField('#username', 'test');
$I->fillField('#password', 'test');
$I->click('Войти');
$I->see('Здравствуй, Тест!');
