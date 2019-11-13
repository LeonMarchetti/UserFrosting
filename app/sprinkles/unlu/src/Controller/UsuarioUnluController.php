<?php
namespace UserFrosting\Sprinkle\Unlu\Controller;

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Admin\Controller\UserController;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Support\Exception\ForbiddenException;

class UsuarioUnluController extends UserController {

    /**
     * Renders a page displaying a user's information, in read-only mode.
     *
     * This checks that the currently logged-in user has permission to view the requested user's info.
     * It checks each field individually, showing only those that you have permission to view.
     * This will also try to show buttons for activating, disabling/enabling, deleting, and editing the user.
     *
     * This page requires authentication.
     * Request type: GET
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @throws ForbiddenException If user is not authozied to access page
     */
    public function pageInfo(Request $request, Response $response, $args) {
        $user = $this->getUserFromParams($args);

        // If the user no longer exists, forward to main user listing page
        if (!$user) {
            throw new NotFoundException();
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'uri_user', [
            'user' => $user,
        ])) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Get a list of all locales
        $locales = $config->getDefined('site.locales.available');

        // Determine fields that currentUser is authorized to view
        $fieldNames = ['user_name', 'name', 'email', 'locale', 'group', 'roles', 'unlu'];

        // Generate form
        $fields = [
            // Always hide these
            'hidden' => ['theme'],
        ];

        // Determine which fields should be hidden
        foreach ($fieldNames as $field) {
            if (!$authorizer->checkAccess($currentUser, 'view_user_field', [
                'user' => $user,
                'property' => $field,
            ])) {
                $fields['hidden'][] = $field;
            }
        }

        // Hide the locale field if there is only 1 locale available
        if (count($config->getDefined('site.locales.available')) <= 1) {
            $fields['hidden'][] = 'locale';
        }

        // Determine buttons to display
        $editButtons = [
            'hidden' => [],
        ];

        if (!$authorizer->checkAccess($currentUser, 'update_user_field', [
            'user' => $user,
            'fields' => ['name', 'email', 'locale'],
        ])) {
            $editButtons['hidden'][] = 'edit';
        }

        if (!$authorizer->checkAccess($currentUser, 'update_user_field', [
            'user' => $user,
            'fields' => ['flag_enabled'],
        ])) {
            $editButtons['hidden'][] = 'enable';
        }

        if (!$authorizer->checkAccess($currentUser, 'update_user_field', [
            'user' => $user,
            'fields' => ['flag_verified'],
        ])) {
            $editButtons['hidden'][] = 'activate';
        }

        if (!$authorizer->checkAccess($currentUser, 'update_user_field', [
            'user' => $user,
            'fields' => ['password'],
        ])) {
            $editButtons['hidden'][] = 'password';
        }

        if (!$authorizer->checkAccess($currentUser, 'update_user_field', [
            'user' => $user,
            'fields' => ['roles'],
        ])) {
            $editButtons['hidden'][] = 'roles';
        }

        if (!$authorizer->checkAccess($currentUser, 'delete_user', [
            'user' => $user,
        ])) {
            $editButtons['hidden'][] = 'delete';
        }

        // Determine widgets to display
        $widgets = [
            'hidden' => [],
        ];

        if (!$authorizer->checkAccess($currentUser, 'view_user_field', [
            'user' => $user,
            'property' => 'permissions',
        ])) {
            $widgets['hidden'][] = 'permissions';
        }

        if (!$authorizer->checkAccess($currentUser, 'view_user_field', [
            'user' => $user,
            'property' => 'activities',
        ])) {
            $widgets['hidden'][] = 'activities';
        }

        return $this->ci->view->render($response, 'pages/user.html.twig', [
            'user'            => $user,
            'locales'         => $locales,
            'fields'          => $fields,
            'tools'           => $editButtons,
            'widgets'         => $widgets,
            'delete_redirect' => $this->ci->router->pathFor('uri_users'),
        ]);
    }
}