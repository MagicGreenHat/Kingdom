<?php
/**
 * @author: Rottenwood
 * @date  : 18.11.15 23:52
 */

$I = new FunctionalTester($scenario);

$result = $I->runSymfonyCommand('kingdom:create:robot');
