<?php
/**
 * @author: Rottenwood
 * @date  : 12.11.15 23:56
 */

$I = new FunctionalTester($scenario);
$I->wantTo('Magically appear in several rooms');

$I->amLoggedInAs('test');

$I->teleportToCoordinates(1, 1);
PHPUnit_Framework_Assert::assertTrue($I->amAtCoordinates(1, 1));

$I->teleportToCoordinates(2, 2);
PHPUnit_Framework_Assert::assertTrue($I->amAtCoordinates(2, 2));

$I->teleportToCoordinates(-5, -3);
PHPUnit_Framework_Assert::assertTrue($I->amAtCoordinates(-5, -3));

$I->teleportToCoordinates(0, 0);
PHPUnit_Framework_Assert::assertTrue($I->amAtCoordinates(0, 0));
