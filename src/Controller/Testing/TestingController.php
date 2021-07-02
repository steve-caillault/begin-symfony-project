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
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
/***/
use App\UI\Pagination\Pagination;

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
     * Page de tests avec des paramètres dans la route
     * @return Response
     */
    #[
        RouteAnnotation(
            path: 'testing/with-params/{param1}',
            name: 'testing_with_params',
            requirements: [
                'param1' => '[^\/]+',
            ],
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' in [ 'dev', 'test' ]",
        )
    ]
    public function testingWithParams() : Response
    {
        return new Response();
    }

    /**
     * Page de test de la pagination
     * @param string $paramType
     * @param string $paramName
     * @param int $itemsPerPage
     * @param int $totalItems
     * @return Response
     */
    #[
        RouteAnnotation(
            path: 'testing/pagination/{paramType}/{paramName}/{itemsPerPage}/{totalItems}/{customPage}',
            name: 'testing_pagination',
            requirements: [
                'paramType' => 'query|route',
                'paramName' => '[^\/]+',
                'itemsPerPage' => '[0-9]+',
                'totalItems' => '[0-9]+',
                'customPage' => '[0-9]+',
            ],
            defaults: [
                'customPage' => 1,
            ],
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' in [ 'dev', 'test' ]",
        )
    ]
    public function testingPagination(
        string $paramType, 
        string $paramName, 
        int $itemsPerPage,
        int $totalItems
    ) : Response
    {
        $pagination = (new Pagination(itemsPerPage: $itemsPerPage, totalItems: $totalItems))
            ->setPageParameterName($paramName)
            ->setPageParameterType($paramType)
        ;

        return $this->render('testing/ui/pagination.html.twig', [
            'pagination' => $pagination,
        ]);
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
            requirements: [ 'errorStatus' => '[0-9]{3}' ],
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' === 'test'"
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

        return $this->forward('App\Controller\ErrorController::index', [
            'exception' => $exception,
        ]);
    }

    /**
     * Page de test de log
     * @param LoggerInterface $logger
     * @param string $message
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/testing/log/{message}',
            name: 'testing_log',
            requirements: [ 'message' => '[^\/]+' ],
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' === 'test'"
        )
    ]
    public function log(LoggerInterface $logger, string $message) : Response
    {
        $logger->debug($message);
        return new Response();
    }
    

}