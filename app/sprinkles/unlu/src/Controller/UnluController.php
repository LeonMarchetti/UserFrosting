<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Sprinkle\Unlu\Database\Models\Peticion;
use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;
use UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu as Usuario;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use Illuminate\Database\Capsule\Manager as Capsule;

class UnluController extends SimpleController {

    public function page(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // // Access-controlled page
        // if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
        //     throw new ForbiddenException();
        // }

        if ($currentUser->id == 1) {
            // Usuario Root
            $vinculaciones = Vinculacion::all();
            $peticiones = Peticion::all();

        } else {
            $vinculaciones = Vinculacion::where('id_solicitante', $currentUser->id)->get();
            $peticiones = Peticion::where('id_usuario', $currentUser->id)->get();
        }

        return $this->ci->view->render($response, 'pages/unlu.html.twig', [
            'vinculaciones' => $vinculaciones,
            'peticiones' => $peticiones,
        ]);
    }

    public function solicitarVinculacion(Request $request, Response $response, $args) {
        // Get POST parameters: user_name, first_name, last_name, email, locale, (group)
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Access-controlled page
        // if (!$authorizer->checkAccess($currentUser, 'create_pastries')) {
        //     throw new ForbiddenException();
        // }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema = new RequestSchema('schema://requests/unlu/solicitar_vinculacion.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        // Asigno el id del usuario actual como id del solicitante de la vinculación
        $data["id_solicitante"] = $currentUser->id;

        // Asigno la fecha actual como fecha de solicitud de la vinculación
        $data["fecha_solicitud"] = date("d-m-Y", time());

        if (!isset($data["responsable"]) || $data["responsable"] === "") {
            $data["responsable"] = $currentUser->full_name;
        }

        if (!isset($data["cargo"]) || $data["cargo"] === "") {

            if ($currentUser->rol === "") {
                $ms->addMessageTranslated('danger', 'UNLU.ROLE.MISSING', $data);
                $error = true;

            } else {
                $data["cargo"] = $currentUser->rol;
            }
        }

        if (!isset($data['tipo_de_usuario']) || $data['tipo_de_usuario'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.TYPE.MISSING', $data);
            $error = true;
        }

        if (!isset($data['actividad']) || $data['actividad'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.ACTIVITY.MISSING', $data);
            $error = true;
        }

        if (!isset($data["telefono"]) || $data["telefono"] === "") {
            if ($currentUser->telefono === "") {
                $ms->addMessageTranslated('danger', 'UNLU.PHONE.MISSING', $data);
                $error = true;

            } else {
                $data["telefono"] = $currentUser->telefono;
            }
        }

        if (!isset($data['fecha_fin']) || $data['fecha_fin'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.END_DATE.MISSING', $data);
            $error = true;

        } else {
            // Comprobar que la fecha de finalización no sea anterior a la fecha de solicitud:
            if (strtotime($data["fecha_fin"]) < strtotime($data["fecha_solicitud"])) {
                $ms->addMessageTranslated('danger', 'UNLU.END_DATE.BEFORE', $data);
                $error = true;
            }
        }

        if (!isset($data['descripcion']) || $data['descripcion'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        if (!isset($data['integrantes'])) {
            $ms->addMessageTranslated('danger', 'UNLU.MEMBERS.MISSING', $data);
            $error = true;

        } else {
            foreach ($data["integrantes"] as $k => $v) {
                if ($v === "") {
                    unset($data["integrantes"][$k]);
                }
            }

            if (empty($data['integrantes'])) {
                $ms->addMessageTranslated('danger', 'UNLU.MEMBERS.MISSING', $data);
                $error = true;
            }

            if (count($data["integrantes"]) !== count(array_flip($data["integrantes"]))) {
                $ms->addMessageTranslated('danger', 'UNLU.MEMBERS.REPEATED', $data);
                $error = true;
            }
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser) {

            $vinculacion = $classMapper->createInstance("vinculacion", $data);
            $vinculacion->save();
            # $vinculacion->id tiene el id generado para esta instancia, si es autoincrement

            foreach ($data["integrantes"] as $id_usuario) {
                $data_integrantes = [
                    "id_usuario" => $id_usuario,
                    "id_vinculacion" => $id_vinculacion,
                    "nombre" => Usuario::find($id_usuario)->full_name
                ];
                $integrante = $classMapper->createInstance("integrante", $data_integrantes);
                //$integrante->save();
            }

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} created a new vinculation {$vinculacion->id}.", [
                'type'    => 'pastry_create',
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.VINCULATION.ADDED', $data);
        });

        return $response->withJson([], 200);
    }

    public function solicitarServicio(Request $request, Response $response, $args) {
        // Get POST parameters: user_name, first_name, last_name, email, locale, (group)
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Access-controlled page
        // if (!$authorizer->checkAccess($currentUser, 'create_pastries')) {
        //     throw new ForbiddenException();
        // }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema = new RequestSchema('schema://requests/unlu/solicitar_servicio.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        // Asigno el id del usuario actual como id del solicitante de la petición
        $data["id_usuario"] = $currentUser->id;

        if (!isset($data['fecha_inicio']) || $data['fecha_inicio'] === "") {
            $ms->addMessageTranslated('warning', 'UNLU.PETITION.START_DATE.MISSING', $data);
            $error = true;

        } else {
            // Comprobar que la fecha de inicio no sea anterior a la fecha actual:
            $fecha_hoy = strtotime(date("d-m-Y", time()));
            if (strtotime($data["fecha_inicio"]) < $fecha_hoy) {
                $ms->addMessageTranslated('warning', 'UNLU.PETITION.START_DATE.BEFORE', $data);
                $error = true;
            }
        }

        if (!isset($data['fecha_fin']) || $data['fecha_fin'] === "") {
            $ms->addMessageTranslated('warning', 'UNLU.PETITION.END_DATE.MISSING', $data);
            $error = true;

        } else {
            // Comprobar que la fecha de finalización no sea anterior a la fecha de solicitud:
            if (strtotime($data["fecha_fin"]) < strtotime($data["fecha_inicio"])) {
                $ms->addMessageTranslated('warning', 'UNLU.PETITION.END_DATE.BEFORE', $data);
                $error = true;
            }
        }

        if (!isset($data["id_servicio"]) || $data["id_servicio"] === "") {
            $ms->addMessageTranslated('warning', 'UNLU.PETITION.SERVICE.MISSING', $data);
            $error = true;
        }

        if (!isset($data["descripcion"]) || $data["descripcion"] === "") {
            $ms->addMessageTranslated('warning', 'UNLU.PETITION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser) {

            try {
                $peticion = $classMapper->createInstance("peticion", $data);
                $peticion->save();

            } catch (\Exception $e) {
                $msg = $e->getMessage();
                Debug::debug("Error: $msg");
            }

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} solicitó una petición {$data->descripcion}.", [
                'type'    => 'pastry_create',
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.PETITION.ADDED', $data);
        });

        return $response->withJson([], 200);
    }

    public function bajaSolicitud(Request $request, Response $response, $args) {

        $params = $request->getParsedBody();

        $peticion = $this->getPeticionFromParams($params);

        if (!$peticion) {
            throw new NotFoundException();
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($peticion, $currentUser) {
            $peticion->delete();
            unset($peticion);

            // Create activity record
            $this->ci->userActivityLogger->info("Usuario {$currentUser->user_name} borró la petición {$peticion->id}.", [
                'type'    => 'pastry_delete',
                'user_id' => $currentUser->id,
            ]);
        });

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $ms->addMessageTranslated('success', 'UNLU.PETITION.DELETE_SUCCESSFUL', [
            'user_name' => $peticon->id,
        ]);

        return $response->withJson([], 200);
    }

    protected function getPeticionFromParams($params) {
        $schema = new RequestSchema("schema://requests/unlu/get-peticion.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        // Get the user to delete
        $peticion = $classMapper
                    ->getClassMapping('peticion')
                    ::where('id', $data['peticion'])
                    ->first();

        return $peticion;
    }
}