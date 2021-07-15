<?php

/**
 * Contrôleur de base pour les appels Ajax
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AjaxController extends AbstractController {

    public const
        STATUS_SUCCESS = 'SUCCESS',
        STATUS_ERROR = 'ERROR'
    ;

    /**********************************************/

    /**
     * Retourne la réponse JSON à retourner
     * @param array $data
     * @param string $status
     * @return JsonResponse
     */
    protected function getAjaxResponse(array $data, string $status = self::STATUS_ERROR) : JsonResponse
    {
        $statusCode = ($status === self::STATUS_SUCCESS) ? 200 : 400;
        return $this->json([
            'status' => $status,
            'data' => $data,
        ], $statusCode);
    }

    /**********************************************/

}