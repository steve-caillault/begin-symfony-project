<?php

/**
 * Contrôleur de test
 */

namespace App\Controller\Testing;

use App\Controller\BaseAjaxController;
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

final class AjaxController extends BaseAjaxController {

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