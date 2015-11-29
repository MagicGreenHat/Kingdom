<?php

const COMMAND_NAME_WEAR = 'wear';

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "wear"');

$I->amLoggedInAs('test');

$result = $I->runCommand('wear newbie-shirt:body');
PHPUnit_Framework_Assert::assertEquals(COMMAND_NAME_WEAR, $result['commandName']);

$result = $I->runCommand('wear coarse-shirt:body');
PHPUnit_Framework_Assert::assertEquals(COMMAND_NAME_WEAR, $result['commandName']);
