<?php

/**
 * Contrôleur d'Ajax
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;

final class AjaxController extends BaseAjaxController
{

    /**
     * Appel par défaut
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/ajax'
        )
    ]
    public function index() : Response
    {
        return $this->getAjaxResponse([
            'success' => true,
        ], self::STATUS_SUCCESS);
    }
}