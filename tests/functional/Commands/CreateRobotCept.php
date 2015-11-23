<?php
/**
 * @author: Rottenwood
 * @date  : 18.11.15 23:52
 */

use Rottenwood\KingdomBundle\Entity\Robot;

$I = new FunctionalTester($scenario);
$I->wantTo('Create robot');

$I->runSymfonyCommand('kingdom:create:robot robot 1');

$I->seeInRepository(Robot::class, ['name' => 'Робот']);
