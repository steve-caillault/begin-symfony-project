<?php

/**
 * Contrôleur d'authentification du panneau d'administration depuis un appel Ajax
 */

namespace App\Controller\Admin\Auth;

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
            path: '/admin/auth/login/ajax',
            name: 'admin_auth_login_ajax',
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