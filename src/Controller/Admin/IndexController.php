<?php

/**
 * Page d'accueil du panneau d'administration
 */

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Security\Admin\WithSecurityUrl;

final class IndexController extends AdminController {

    /**
     * Index du panneau d'administration
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/admin',
            name: 'admin_index',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function index() : Response
    {
        return new Response('Admin index');
    }

}