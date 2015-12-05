<?php

$I = new FunctionalTester($scenario);
$I->wantTo('Execute command "move"');

$I->amLoggedInAs('test');

$I->runCommand('move north');
$I->runCommand('move east');
$I->runCommand('move south');
$I->runCommand('move west');
