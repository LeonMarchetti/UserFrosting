<?php

namespace UserFrosting\Sprinkle\Pastries\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Pastries\Database\Models\Pastry;
use UserFrosting\Sprinkle\Core\Facades\Debug;

class PastriesController extends SimpleController
{
    public function pageList(Request $request, Response $response, $args)
    {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
            throw new ForbiddenException();
        }

        $pastries = Pastry::all();

        // Debug::debug($pastries);

        return $this->ci->view->render($response, 'pages/pastries.html.twig', [
            'pastries' => $pastries
        ]);
    }

    public function addPastryModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
            throw new ForbiddenException();
        }

        $data = [
            "nombre" => "Postre",
            "origen" => "Origen",
            "descripcion" => "Descripción"
        ];

        return $this->ci->view->render($response, 'modals/pastries.html.twig'. [
            "pastry" => $data,
            "form" => [
                "action" => "api/pastries",
                "method" => "POST",
                "submit_text" => "Agregar"
            ]
        ]);
    }

    public function addPastry(Request $request, Response $response, $args) {
        Debug::debug("addPastry");

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

        $schema = new RequestSchema('schema://requests/pastries/create.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        if (!isset($data['pastry_name']) ||
            !isset($data["pastry_origin"])) {

            $error = true;
        }

        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser) {
            // Create the pastry
            $pastry = $classMapper->createInstance("pastry", $data);

            // Store new pastry to database
            $pastry->save();

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} created a new pastry {$data->pastry_name}.", [
                'type'    => 'pastry_create',
                'user_id' => $currentUser->id,
            ]);
        });
    }
}