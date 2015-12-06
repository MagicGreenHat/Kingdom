<?php

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "obtainWood"');

$I->amLoggedInAs('test');
$I->haveNoWaitState();
$I->teleportToCoordinates(3, 3);

$I->amAtCoordinates(3, 3);

$result = $I->runCommand('obtainWood');
PHPUnit_Framework_Assert::assertEquals(1, $result['data']['obtained']);
PHPUnit_Framework_Assert::assertEquals(9, $result['data']['resources']['wood']);
PHPUnit_Framework_Assert::assertFalse(isset($result['waitstate']));

$result = $I->runCommand('obtainWood');
PHPUnit_Framework_Assert::assertTrue(isset($result['waitstate']));
