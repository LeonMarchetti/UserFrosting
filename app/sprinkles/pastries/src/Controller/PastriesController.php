<?php

namespace UserFrosting\Sprinkle\Pastries\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Pastries\Database\Models\Pastry;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use Illuminate\Database\Capsule\Manager as Capsule;

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

    public function addPastry(Request $request, Response $response, $args) {
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

        $schema = new RequestSchema('schema://requests/pastries/add.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        if (!isset($data['name']) || $data['name'] === "") {
            $ms->addMessageTranslated('danger', 'PASTRIES.NAME.MISSING', $data);
            $error = true;
        }

        if (!isset($data['origin']) || $data['origin'] === "") {
            $ms->addMessageTranslated('danger', 'PASTRIES.ORIGIN.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser) {
            // Create the pastry
            $pastry = $classMapper->createInstance("pastry", $data);

            // Store new pastry to database
            $pastry->save();

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} created a new pastry {$data->name}.", [
                'type'    => 'pastry_create',
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'PASTRIES.ADDED', $data);
        });

        return $response->withJson([], 200);
    }

    public function deletePastry(Request $request, Response $response, $args) {

        $pastry = $this->getPastryFromParams($args);

        // If the pastry doesn't exist, return 404
        if (!$pastry) {
            throw new NotFoundException();
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        $pastryName = $pastry->name;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($pastry, $pastryName, $currentUser) {
            $pastry->delete();
            unset($pastry);

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} deleted the pastry for {$pastryName}.", [
                'type'    => 'pastry_delete',
                'user_id' => $currentUser->id,
            ]);
        });

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $ms->addMessageTranslated('success', 'PASTRIES.DELETION_SUCCESSFUL', [
            'user_name' => $pastryName,
        ]);

        return $response->withJson([], 200);
    }

    public function editPastry(Request $request, Response $response, $args)
    {
        // Get the username from the URL
        $pastry = $this->getPastryFromParams($args);

        if (!$pastry) {
            throw new NotFoundException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Get PUT parameters
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        // Load the request schema
        $schema = new RequestSchema('schema://requests/pastries/edit-info.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $error = false;

        if (!isset($data['name']) || $data['name'] === "") {
            $ms->addMessageTranslated('danger', 'PASTRIES.NAME.MISSING', $data);
            $error = true;
        }

        if (!isset($data['origin']) || $data['origin'] === "") {
            $ms->addMessageTranslated('danger', 'PASTRIES.ORIGIN.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($data, $pastry, $currentUser) {
            // Update the user and generate success messages
            foreach ($data as $name => $value) {
                if ($value != $pastry->$name) {
                    $pastry->$name = $value;
                }
            }

            $pastry->save();

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} updated basic account info for pastry {$pastry->name}.", [
                'type'    => 'account_update_info',
                'user_id' => $currentUser->id,
            ]);
        });

        $ms->addMessageTranslated('success', 'DETAILS_UPDATED', [
            'user_name' => $pastry->name,
        ]);

        return $response->withJson([], 200);
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