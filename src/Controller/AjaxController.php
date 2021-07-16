<?php

/**
 * Contrôleur d'Ajax
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Service\AjaxResponseService;

final class AjaxController extends BaseAjaxController
{

    /**
     * Appel par défaut
     * @param AjaxResponseService $ajaxResponseService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/ajax'
        )
    ]
    public function index(AjaxResponseService $ajaxResponseService) : Response
    {
        return $ajaxResponseService->getFormatting([
            'success' => true,
        ], AjaxResponseService::STATUS_SUCCESS);
    }
}