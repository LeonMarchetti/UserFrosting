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
use UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu as Usuario;
use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use Illuminate\Database\Capsule\Manager as Capsule;


class UnluModalController extends SimpleController {

    public function solicitarVinculacionModal(Request $request, Response $response, $args) {

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        // Valores por defecto de la vinculación que provienen de los datos del usuario actual.
        $vinculacion = [
            "responsable" => $currentUser->full_name,
            "cargo" => $currentUser->rol,
            "correo" => $currentUser->email,
            "telefono" => $currentUser->telefono,

            // Valores de prueba para solicitar vinculación, comentar para producción:
            // "actividad" => "Prueba integrantes",
            // "fecha_fin" => "2020-01-01",
            // "tipo_de_usuario" => 3,
            // "descripcion" => "Prueba de integrantes",
        ];

        // Lista de tipos de usuario
        $tipos_de_usuario = TipoUsuario::all();
        $usuarios = Usuario::all();

        return $this->ci->view->render($response, 'modals/vinculacion.html.twig', [
            "vinc" => $vinculacion,
            "tipos_de_usuario" => $tipos_de_usuario,
            "usuarios" => $usuarios,
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
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        $servicios = Servicio::all();
        $vinculaciones = Vinculacion::all();

        return $this->ci->view->render($response, 'modals/peticion.html.twig', [
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
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        $peticiones = Peticion::where('id_usuario', $currentUser->id)->get();

        return $this->ci->view->render($response, 'modals/baja-solicitud.html.twig', [
            "peticiones" => $peticiones,
            "form" => [
                "action" => "api/unlu/baja-solicitud",
                "method" => "POST",
                "submit_text" => "Borrar"
            ]
        ]);
    }

    public function aprobarPeticionModal(Request $request, Response $response, $args) {

        // GET parameters
        $params = $request->getQueryParams();

        $peticion = $this->getPeticionFromParams($params);
        if (!$peticion) {
            throw new NotFoundException();
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        return $this->ci->view->render($response, 'modals/aprobar-peticion.html.twig', [
            'peticion' => $peticion,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
            ],
        ]);
    }

    public function editarPeticionModal(Request $request, Response $response, $args) {

        // GET parameters
        $params = $request->getQueryParams();

        $peticion = $this->getPeticionFromParams($params);
        if (!$peticion) {
            throw new NotFoundException();
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        $servicios = Servicio::all();
        $vinculaciones = Vinculacion::all();

        return $this->ci->view->render($response, 'modals/editar-peticion.html.twig', [
            "edicion" => true,
            'peticion' => $peticion,
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
                "method" => "POST",
                "submit_text" => "Actualizar"
            ],
        ]);
    }

    protected function getPeticionFromParams($params) {
        $schema = new RequestSchema("schema://requests/unlu/peticion/get-by-id.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $peticion = Peticion::find($data["id"]);

        return $peticion;
    }
}