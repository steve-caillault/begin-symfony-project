<?php

/**
 * Contrôleur de test
 */

namespace App\Controller\Testing;

use App\Controller\AjaxController as BaseController;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\HttpFoundation\{
    Response,
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
     * @return JsonResponse
     */
    #[
        RouteAnnotation(
            path: '/ajax',
            methods: [ 'GET' ]
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
     * @return JsonResponse
     */
    #[
        RouteAnnotation(
            path: '/admin/ajax',
            methods: [ 'GET' ]
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
            path: '/error-{errorStatus}/ajax',
            requirements: [ 'errorStatus' => '[0-9]{3}' ],
            methods: [ 'GET' ]
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

}