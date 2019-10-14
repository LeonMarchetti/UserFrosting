<?php

$app->group('/unlu', function () {
    $this->get('', 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:page');
})->add('authGuard');

$app->group('/solicitar-vinculacion', function () {
    $this->get('', 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:solicitar_vinculacion');
})->add('authGuard');

$app->group('/solicitar-servicio', function () {
    $this->get('', 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:solicitar_servicio');
})->add('authGuard');

$app->group('/baja-solicitud', function () {
    $this->get('', 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:baja_solicitud');
})->add('authGuard');