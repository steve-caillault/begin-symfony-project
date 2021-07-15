<?php

/**
 * Gestion de la réponse en cas d'erreur en Ajax sur l'authentification
 */

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AuthAjaxSubscriber implements EventSubscriberInterface
{
    /**
     * Constructeur
     * @param TranslatorInterface $translator
     */
    public function __construct(private TranslatorInterface $translator)
    {

    }

    /**
     * @param ResponseEvent $event
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event) : void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // On ne gére que l'appel Ajax à l'authentification
        $route = $request->attributes->get('_route');
        if($route !== 'app_admin_security_ajax_login')
        {
            return;
        }

        // On souhaite gérer uniquement les erreurs 401
        if($response->getStatusCode() !== 401)
        {
            return;
        }

        // Modifie la réponse avec notre formatage
        $response->setContent(json_encode([
            'status' => \App\Controller\AjaxController::STATUS_ERROR,
            'data' => [
                'error' => $this->translator->trans('credentials.invalid', domain: 'security'),
            ]
        ]));
    }

    /**
     * Evénements à gérer
     * @return array
     */
    public static function getSubscribedEvents() : array
    {
        return [
            // The custom listener must be called before LocaleListener
            'kernel.response' => ['onKernelResponse', 50],
        ];
    }
}