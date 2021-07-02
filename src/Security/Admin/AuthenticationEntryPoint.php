<?php

/**
 * Point d'entrée de l'authentification du panneau d'administration
 */

namespace App\Security\Admin;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\{ 
    Request, 
    JsonResponse, 
    RedirectResponse 
};

final class AuthenticationEntryPoint implements AuthenticationEntryPointInterface {

    /**
     * Générateur d'url
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * Modifie le générateur d'URL
     * @param UrlGeneratorInterface $urlGenerator
     * @return void
     * @required
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator) : void
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $loginUrl = $this->urlGenerator->generate(
            'admin_auth_login', 
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL
        );

        if($request->isXmlHttpRequest())
        {
            return new JsonResponse([
                'data' => [
                    'status' => \App\Controller\AjaxController::STATUS_ERROR,
                    'login_url' => $loginUrl,
                ],
            ], 401);
        }
        else
        {
            return new RedirectResponse($loginUrl);
        }
    }

}