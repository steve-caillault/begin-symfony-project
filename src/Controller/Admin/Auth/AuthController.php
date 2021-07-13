<?php

/**
 * Contrôleur d'authentification du panneau d'administration
 */

namespace App\Controller\Admin\Auth;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\{
    Request, Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;

use Symfony\Component\HttpFoundation\Cookie;
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
            path: '/auth/login',
            name: 'admin_auth_login',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function login(Request $request, AuthenticationUtils $authenticationUtils) : Response
    {
        if($this->getUser() !== null)
        {
            return $this->redirectToRoute('admin_index');
        }

        // get the login error if there is one
        $error = ($authenticationUtils->getLastAuthenticationError() !== null);

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

    /**
     * Déconnexion
     * @return void
     */
    #[
        RouteAnnotation(
            path: '/admin/auth/logout',
            name: 'admin_auth_logout',
            methods: [ 'GET' ]
        )
    ]
    public function logout() : void
    {
        // Gérée par Symfony
    }

}