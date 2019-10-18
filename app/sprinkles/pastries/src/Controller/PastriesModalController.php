<?php

namespace UserFrosting\Sprinkle\Pastries\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Pastries\Database\Models\Pastry;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use Illuminate\Database\Capsule\Manager as Capsule;

class PastriesModalController extends SimpleController
{
    public function addPastryModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
            throw new ForbiddenException();
        }

        // Datos de prueba, dejar vacío para producción
        $data = [
            "name" => "Chocotorta",
            "origin" => "Argentina",
            "description" => "Torta de galletitas de chocolate con dulce de leche y crema de queso"
        ];

        return $this->ci->view->render($response, 'modals/pastries.html.twig', [
            "pastry" => $data,
            "form" => [
                "action" => "api/pastries",
                "method" => "POST",
                "submit_text" => "Agregar"
            ]
        ]);
    }

    public function deletePastryModal(Request $request, Response $response, $args) {

        // GET parameters
        $params = $request->getQueryParams();

        $pastry = $this->getPastryFromParams($params);

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        return $this->ci->view->render($response, 'modals/confirm-delete-pastry.html.twig', [
            'pastry' => $pastry,
            'form' => [
                'action' => "api/pastries/u/{$pastry->name}",
            ],
        ]);
    }

    public function editPastryModal(Request $request, Response $response, $args) {

        // GET parameters
        $params = $request->getQueryParams();

        $pastry = $this->getPastryFromParams($params);

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        return $this->ci->view->render($response, 'modals/pastries.html.twig', [
            'pastry' => $pastry,
            'form' => [
                'action' => "api/pastries/u/{$pastry->name}",
                'method' => "PUT",
                "submit_text" => "Actualizar"
            ],
        ]);
    }

    protected function getPastryFromParams($params) {
        $schema = new RequestSchema("schema://requests/pastries/get-by-name.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        // Get the user to delete
        $pastry = $classMapper
                    ->getClassMapping('pastry')
                    ::where('name', $data['name'])
                    ->first();

        return $pastry;
    }
}