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
    $this->get('/delete', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:deletePastryModal');
    $this->get('/edit', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:editPastryModal');
})->add('authGuard');

$app->group("/api/pastries", function () {
    $this->post('', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:addPastry');
    $this->delete('/u/{name}', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:deletePastry');
    $this->put('/u/{name}', 'UserFrosting\Sprinkle\Pastries\Controller\PastriesController:editPastry');
});