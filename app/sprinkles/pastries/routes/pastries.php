<?php

/**
 * Routes for pastry-related pages.
 */
$app->group('/pastries', function () {
    $this->get('', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:pageList')
         ->setName('pastries');
})->add('authGuard');

$app->group("/modals/pastries", function () {
    $this->get('/add', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:addPastryModal');
})->add('authGuard');

$app->group("/api/pastries", function () {
    $this->get('', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:addPastry');
});