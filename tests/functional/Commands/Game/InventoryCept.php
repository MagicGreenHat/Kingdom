<?php

const COMMAND_NAME = 'inventory';

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "inventory"');

$I->amLoggedInAs('test');

$result = $I->runCommand('inventory');
PHPUnit_Framework_Assert::assertEquals(COMMAND_NAME, $result['commandName']);
PHPUnit_Framework_Assert::assertCount(6, $result['data']);

$savedItems = $I->getAllItems();
$I->deleteAllItems();

$result = $I->runCommand('inventory');
PHPUnit_Framework_Assert::assertEquals(COMMAND_NAME, $result['commandName']);
PHPUnit_Framework_Assert::assertFalse(isset($result['data']));

$I->loadAllItems($savedItems);
