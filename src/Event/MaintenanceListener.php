<?php

/**
 * Gestion de la maintenance
 */

namespace App\Event;

use Twig\Environment as Twig;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\{ 
    Response, 
    JsonResponse 
};
/***/
use App\SiteService;

final class MaintenanceListener {

    /**
     * Constructeur
     * @param Twig $twig
     * @param SiteService $siteService
     */
    public function __construct(private Twig $twig, private SiteService $siteService)
    {
        
    }

    /**
     * Pour chaque requête
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event) : void
    {
        $this->siteService->setRequest($event->getRequest());

    	// Si on se trouve sur une page du panneau d'administration, on n'affiche pas la maintenance
        $isAdminSection = ($this->siteService->isAdminSection());
    	if($isAdminSection)
    	{
    		return;
    	}

        // Le site n'est pas en maintenance, on s'arrête
    	if(! $this->siteService->getMaintenanceEnabled())
        {
            return;
        }

        $responseCode = Response::HTTP_SERVICE_UNAVAILABLE;
        $isAjax = $event->getRequest()->isXmlHttpRequest();
        if($isAjax)
        {
            $response = new JsonResponse([
                'status' => \App\Controller\AjaxController::STATUS_ERROR,
                'data' => [
                    'maintenance' => true,
                ],
            ], $responseCode);
        }
        else
        {
            $content = $this->twig->render('layout/maintenance.html.twig');
            $response = new Response($content, $responseCode);
        }

        $event->setResponse($response);
        $event->stopPropagation();
    }

}