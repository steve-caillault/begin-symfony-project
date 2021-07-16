<?php

/**
 * Contrôleur de test
 */

namespace App\Controller\Testing;

use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\{
    UnauthorizedHttpException,
    AccessDeniedHttpException,
    NotFoundHttpException,
    HttpException,
    
};
/***/
use App\Controller\BaseAjaxController;

final class AjaxController extends BaseAjaxController {

    /**
     * Page de test d'erreur
     * @param int $errorStatus
     * @return JsonResponse
     */
    #[
        RouteAnnotation(
            path: '/error-{errorStatus}/ajax',
            requirements: [ 'errorStatus' => '[0-9]{3}' ],
            methods: [ 'GET' ]
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

        return $this->forward('App\Controller\ErrorController::index', [
            'exception' => $exception,
        ]);
    }

}