<?php

use Rottenwood\KingdomBundle\Entity\Human;

$I = new FunctionalTester($scenario);
$I->wantTo('Create user');

$I->runSymfonyCommand('kingdom:create:user testeress password123 testeress@test.com female');

$I->seeInRepository(Human::class,
    [
        'name'   => 'Тестересс',
        'email'  => 'testeress@test.com',
        'gender' => 'female',
    ]
);
