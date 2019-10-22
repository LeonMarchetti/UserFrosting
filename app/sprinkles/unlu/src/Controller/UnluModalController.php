<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;
use UserFrosting\Sprinkle\Unlu\Database\Models\Peticion;

class UnluModalController extends SimpleController
{
    public function solicitarVinculacionModal(Request $request, Response $response, $args) {

    }

    public function solicitarServicioModal(Request $request, Response $response, $args) {
    }

    public function bajaSolicitudModal(Request $request, Response $response, $args) {
    }

}