<?php
/**
 * @author: Rottenwood
 * @date  : 23.11.15 22:52
 */

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
