<?php

/**
 * Contrôleur de test
 */

namespace App\Controller\Testing;

use App\Controller\AjaxController as BaseController;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\HttpFoundation\{
    Request,
    JsonResponse
};
use Symfony\Component\HttpKernel\Exception\{
    UnauthorizedHttpException,
    AccessDeniedHttpException,
    NotFoundHttpException,
    HttpException,
    
};

final class AjaxController extends BaseController {

    /**
     * Page de test
     * @return Response
     */
    #[
        RouteAnnotation(
            path: 'testing/ajax',
            name: 'testing_ajax',
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' in [ 'dev', 'test' ]"
        )
    ]
    public function index() : JsonResponse
    {
        return $this->getAjaxResponse([
            'success' => true,
        ], self::STATUS_SUCCESS);
    }

    /**
     * Page de test du panneau d'administration
     * @return Response
     */
    #[
        RouteAnnotation(
            path: 'testing/admin/ajax',
            name: 'testing_admin_ajax',
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' in [ 'dev', 'test' ]"
        )
    ]
    public function admin() : JsonResponse
    {
        return $this->getAjaxResponse([
            'admin' => true,
        ], self::STATUS_SUCCESS);
    }

    /**
     * Page de test d'erreur
     * @param int $errorStatus
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/testing/error-{errorStatus}/ajax',
            name: 'testing_error_ajax',
            methods: [ 'GET' ],
            condition: "'%kernel.environment%' === 'test'",
            requirements: [ 'errorStatus' => '[0-9]{3}' ]
        )
    ]
    public function error(int $errorStatus) : JsonResponse
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