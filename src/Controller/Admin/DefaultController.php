<?php

/**
 * Page d'accueil du panneau d'administration
 */

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;

final class DefaultController extends AdminController {

    /**
     * Index du panneau d'administration
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function index() : Response
    {
        return new Response('Admin index');
    }

}