<?php
$I = new FunctionalTester($scenario);
$I->wantTo('change language on homepage');
$I->amOnPage('/?lang=pt');
$I->see('Olá mundo');
$I->sendGET("/setLang/EN");
$I->seeResponseCodeIs(200);
$I->see('Hello world');
