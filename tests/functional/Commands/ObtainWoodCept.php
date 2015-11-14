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

$result = $I->runCommand('obtainWood');
PHPUnit_Framework_Assert::assertEquals(1, $result['data']['obtained']);
PHPUnit_Framework_Assert::assertEquals(8, $result['data']['resources']['wood']);

$result = $I->runCommand('obtainWood');
PHPUnit_Framework_Assert::assertEquals(1, $result['data']['obtained']);
PHPUnit_Framework_Assert::assertEquals(7, $result['data']['resources']['wood']);


foreach (range(1, 10) as $i) {
    $result = $I->runCommand('obtainWood');
}

PHPUnit_Framework_Assert::assertEquals('obtainWood', $result['commandName']);
PHPUnit_Framework_Assert::assertFalse(isset($result['data']));
