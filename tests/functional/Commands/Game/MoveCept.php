<?php

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "move"');

$I->amLoggedInAs('test');

$I->haveNoWaitState();
$I->runCommand('move north');

$result = $I->runCommand('move east');
PHPUnit_Framework_Assert::assertTrue(isset($result['waitstate']));

$I->haveNoWaitState();
$I->runCommand('move east');

$I->haveNoWaitState();
$I->runCommand('move south');

$I->haveNoWaitState();
$I->runCommand('move west');
