<?php

/**
 * Contrôleur de test
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\HttpFoundation\Response;

final class TestingController extends AbstractController {

    /**
     * Page de test
     * @return Response
     */
    #[
        RouteAnnotation(
            path: 'testing',
            name: 'testing',
            methods: [ 'GET' ],
            condition: '\'%kernel.environment%\' === \'dev\''
        )
    ]
    public function index() : Response
    {
        return new Response('Testing Page');
    }

}