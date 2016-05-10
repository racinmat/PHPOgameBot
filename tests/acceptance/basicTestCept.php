<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');
$exception = new ActorException();
$exception->actor = $I;
throw $exception;