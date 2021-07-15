<?php

/**
 * Contrôleur d'authentification du panneau d'administration depuis un appel Ajax
 */

namespace App\Controller\Admin\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Controller\AjaxController as BaseAjaxController;

final class AjaxController extends BaseAjaxController {

    /**
     * Connexion en Ajax
     * @return JsonResponse
     */
    #[
        RouteAnnotation(
            path: '/auth/login/ajax',
            methods: [ 'POST' ]
        )
    ]
    public function login() : JsonResponse
    {
        $logged = ($this->getUser()?->getId() !== null);

        $responseStatus = ($logged) ? self::STATUS_SUCCESS : self::STATUS_ERROR;

        return $this->getAjaxResponse([
            'logged' => $logged,
        ], $responseStatus);
    }

}