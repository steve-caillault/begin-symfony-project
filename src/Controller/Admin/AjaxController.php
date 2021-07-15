<?php

/**
 * Appel Ajax du panneau d'administration
 */

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Controller\AjaxController as BaseAjaxController;

final class AjaxController extends BaseAjaxController {

    /**
     * Appel Ajax du panneau d'administration
     * @return Response
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

}