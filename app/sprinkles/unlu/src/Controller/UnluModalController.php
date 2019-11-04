<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Sprinkle\Unlu\Database\Models\Peticion;
use UserFrosting\Sprinkle\Unlu\Database\Models\Servicio;
use UserFrosting\Sprinkle\Unlu\Database\Models\TipoUsuario;
use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;


class UnluModalController extends SimpleController {

    public function solicitarVinculacionModal(Request $request, Response $response, $args) {

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        // if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
        //     throw new ForbiddenException();
        // }

        // Valores por defecto de la vinculación que provienen de los datos del usuario actual.
        $vinculacion = [
            "responsable" => $currentUser->full_name,
            "cargo" => $currentUser->rol,
            "correo" => $currentUser->email,
            "telefono" => $currentUser->telefono,
        ];

        // Valores de prueba para solicitar vinculación, comentar para producción:
        // $vinculacion["actividad"] = "Antes";
        // $vinculacion["fecha_fin"] = "2019-01-01";
        // $vinculacion["descripcion"] = "Fecha fin antes que la fecha actual";

        // Lista de tipos de usuario
        $tipos_de_usuario = TipoUsuario::all();

        return $this->ci->view->render($response, 'modals/vinculacion.html.twig', [
            "vinc" => $vinculacion,
            "tipos_de_usuario" => $tipos_de_usuario,
            "form" => [
                "action" => "api/unlu",
                "method" => "POST",
                "submit_text" => "Solicitar"
            ]
        ]);
    }

    public function solicitarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        // if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
        //     throw new ForbiddenException();
        // }

        $servicios = Servicio::all();
        $vinculaciones = Vinculacion::all();

        // Datos de prueba, dejar vacío para producción
        $peticion = [
            "fecha_inicio" => "2019-11-10",
            "fecha_fin" => "2019-11-30",
            "id_servicio" => 1,
            "id_vinculacion" => 28,
            "descripcion" => "Prueba",
        ];

        return $this->ci->view->render($response, 'modals/peticion.html.twig', [
            "peticion" => $peticion,
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            "form" => [
                "action" => "api/unlu/peticion",
                "method" => "POST",
                "submit_text" => "Solicitar"
            ]
        ]);
    }

    public function bajaSolicitudModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        // if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
        //     throw new ForbiddenException();
        // }

        $peticiones = Peticion::where('id_usuario', $currentUser->id)->get();
        $servicios = Servicio::all();

        foreach ($peticiones as $peticion) {
            $peticion->servicio = $servicios->find($peticion->id_servicio)->denominacion;
        }

        return $this->ci->view->render($response, 'modals/baja-solicitud.html.twig', [
            "peticiones" => $peticiones,
            "form" => [
                "action" => "api/unlu/baja-solicitud",
                "method" => "POST",
                "submit_text" => "Borrar"
            ]
        ]);
    }

}