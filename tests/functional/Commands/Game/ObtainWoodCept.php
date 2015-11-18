<?php
/**
 * @author: Rottenwood
 * @date  : 12.11.15 23:17
 */

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "obtainWood"');

$I->amLoggedInAs('test');
$I->teleportToCoordinates(3, 3);

$I->amAtCoordinates(3, 3);

$result = $I->runCommand('obtainWood');
PHPUnit_Framework_Assert::assertEquals(1, $result['data']['obtained']);
PHPUnit_Framework_Assert::assertEquals(9, $result['data']['resources']['wood']);
PHPUnit_Framework_Assert::assertFalse(isset($result['data']['waitstate']));

$result = $I->runCommand('obtainWood');
PHPUnit_Framework_Assert::assertTrue(isset($result['data']['waitstate']));
