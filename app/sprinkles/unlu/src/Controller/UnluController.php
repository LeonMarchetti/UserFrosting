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

class UnluController extends SimpleController
{
    public function page(Request $request, Response $response, $args)
    {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'see_pastries')) {
            throw new ForbiddenException();
        }

        // Debug::debug($currentUser);

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
}