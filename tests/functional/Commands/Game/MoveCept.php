<?php

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "move"');

$I->amLoggedInAs('test');

$I->haveNoWaitState();
$I->runCommand('move north');

$I->haveNoWaitState();
$I->runCommand('move east');

$I->haveNoWaitState();
$I->runCommand('move south');

$I->haveNoWaitState();
$I->runCommand('move west');
