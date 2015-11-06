<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('страница логина отображается корректно');
$I->amOnPage('/');
$I->see('Войти');
$I->see('Регистрация');
