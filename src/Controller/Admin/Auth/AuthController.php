<?php

/**
 * Contrôleur d'authentification du panneau d'administration
 */

namespace App\Controller\Admin\Auth;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Controller\Admin\AdminController;

final class AuthController extends AdminController {

    /**
     * Connexion
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/admin/auth/login',
            name: 'admin_auth_login',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function login(AuthenticationUtils $authenticationUtils) : Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->renderView('forms/auth.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'ajaxLoginUrl' => $this->generateUrl('admin_auth_login_ajax'),
        ]);

        return $this->render('admin/auth.html.twig', [
            'form' => $form,
        ]);
    }

}