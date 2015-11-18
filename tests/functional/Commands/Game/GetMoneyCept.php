<?php
/**
 * Author: Rottenwood
 * Date Created: 08.11.15 1:24
 */

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "getMoney"');

$I->amLoggedInAs('test');

$I->setMoney(0, 0);
$I->haveMoney(0, 0);

$I->setMoney(5, 5);
$I->haveMoney(5, 5);
