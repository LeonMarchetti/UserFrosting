<?php

namespace UserFrosting\Sprinkle\Site\Controller;

use UserFrosting\Sprinkle\Core\Controller\SimpleController;

class PageController extends SimpleController
{
    public function pageMembers($request, $response, $args)
    {
        return $this->ci->view->render($response, 'pages/members.html.twig');
    }
}
