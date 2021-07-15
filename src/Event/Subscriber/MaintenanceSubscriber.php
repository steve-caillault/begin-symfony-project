<?php

/**
 * Gestion de la réponse en cas d'état de maintenance
 */

namespace App\Event\Subscriber;

use Twig\Environment as Twig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\{
	Response,
	JsonResponse
};

final class MaintenanceSubscriber implements EventSubscriberInterface
{
	/**
	 * Constructeur
	 * @param Twig $twig
	 * @param string $maintenanceFilePath Chemin d'accès au fichier de la maintenance
	 */
	public function __construct(private Twig $twig, private string $maintenanceFilePath)
	{
		
	}
	
	/**
	 * @param RequestEvent $event
	 * @return void
	 */
	public function onKernelRequest(RequestEvent $event) : void
	{
		$response = $event->getResponse();
		$request = $event->getRequest();
		
		// Si on se trouve sur une page du panneau d'administration, on n'affiche pas la maintenance
		$routeName = $request->attributes->get('_route');
		$isAdminRoute = (str_contains($routeName, 'admin_'));
		if($isAdminRoute)
		{
			return;
		}
		
		// Le site n'est pas en maintenance, on s'arrête
		if(! file_exists($this->maintenanceFilePath))
		{
			return;
		}
		
		$responseCode = Response::HTTP_SERVICE_UNAVAILABLE;
        $isAjax = $request->isXmlHttpRequest();
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
	
	/**
	 * Evénements à gérer
	 * @return array
	 */
	public static function getSubscribedEvents() : array
	{
		return [
			'kernel.request' => ['onKernelRequest'],
		];
	}
}