<?php

use Rottenwood\KingdomBundle\Entity\Robot;

$I = new FunctionalTester($scenario);
$I->wantTo('Create robot');

$I->runSymfonyCommand('kingdom:create:robot robot 1');

$I->seeInRepository(Robot::class, ['name' => 'Робот']);
