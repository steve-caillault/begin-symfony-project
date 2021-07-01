<?php

/**
 * Contrôleur de test
 */

namespace App\Controller\Testing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\HttpKernel\Exception\{
    UnauthorizedHttpException,
    AccessDeniedHttpException,
    NotFoundHttpException,
    HttpException,
    
};
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
            condition: "'%kernel.environment%' in [ 'dev', 'test' ]"
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
            name: 'testing_pagination',
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' in [ 'dev', 'test' ]",
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

    /**
     * Page de test de l'administration
     * @return Response
     */
    #[
        RouteAnnotation(
            path: 'testing/admin',
            name: 'testing_admin_index',
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' in [ 'dev', 'test' ]"
        )
    ]
    public function testingAdmin() : Response
    {
        return new Response('admin');
    }

    /**
     * Page de test d'erreur
     * @param int $errorStatus
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/testing/error-{errorStatus}',
            name: 'testing_error',
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' === 'test'",
            requirements: [ 'errorStatus' => '[0-9]{3}' ]
        )
    ]
    public function error(int $errorStatus) : Response
    {
        
        $exception = match($errorStatus) {
            401 => new UnauthorizedHttpException(''),
            403 => new AccessDeniedHttpException(),
            404 => new NotFoundHttpException(),
            default => new HttpException(500)
        };

        $response = $this->forward('App\Controller\ErrorController::index', [
            'exception' => $exception,
        ]);

        return $response;

    }
    

}