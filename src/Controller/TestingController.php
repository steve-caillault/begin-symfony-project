<?php

/**
 * Contrôleur de test
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

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

    /**
     * Page de test de la pagination
     * @param Request $request
     * @return Response
     */
    #[
        RouteAnnotation(
            path: 'testing/pagination/{param1}/{customPage}',
            name: 'testing.pagination',
            methods: [ 'GET' ],
            condition: '\'%kernel.environment%\' === \'dev\'',
            defaults: [ 
                'param1' => 'value1',
                'customPage' => 1
            ],
            requirements: [ 
                'param1' => '[^\/]+',
                'customPage' => '[0-9]+' 
            ],
        )
    ]
    public function testingPagination(Request $request) : Response
    {
        return new Response();
    }

}