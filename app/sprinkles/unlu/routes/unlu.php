<?php

$app->group('/unlu', function () {
    $this->get('', 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:page');
})->add('authGuard');

$app->group('/modals/unlu', function () {
    $this->get('/solicitar-vinculacion', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:solicitarVinculacionModal');
    $this->get('/solicitar-servicio', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:solicitarServicioModal');
    $this->get('/baja-solicitud', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:bajaSolicitudModal');
})->add('authGuard');

$app->group("/api/unlu", function() {
    $this->post("", 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:solicitarVinculacion');
});