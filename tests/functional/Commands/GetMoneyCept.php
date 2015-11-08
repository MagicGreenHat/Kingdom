<?php
/**
 * Author: Rottenwood
 * Date Created: 08.11.15 1:24
 */

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "getMoney"');

$I->amLoggedInAs('test');

$I->setMoney(0, 0);

$result = $I->runCommand('getMoney');

PHPUnit_Framework_Assert::assertEquals(0, $result['gold']);
PHPUnit_Framework_Assert::assertEquals(0, $result['silver']);

$I->setMoney(5, 5);

$result = $I->runCommand('getMoney');

PHPUnit_Framework_Assert::assertEquals(5, $result['gold']);
PHPUnit_Framework_Assert::assertEquals(5, $result['silver']);
