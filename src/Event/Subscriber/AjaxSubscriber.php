<?php

/**
 * Vérification qu'un appel Ajax est appelé en Ajax
 */

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\{
	Response,
	JsonResponse
};
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/***/
use App\Controller\AjaxController;

final class AjaxSubscriber implements EventSubscriberInterface
{
	
    /**
     * Constructeur
     * @param KernelInterface $kernel 
     */
    public function __construct(private KernelInterface $kernel)
    {

    }

	/**
	 * @param RequestEvent $event
	 * @return void
	 */
	public function onKernelRequest(RequestEvent $event) : void
	{
		$request = $event->getRequest();
        $controllerParam = $request->attributes->get('_controller');
        $controllerData = explode('::', $controllerParam);
        $controllerClassName = $controllerData[0] ?? null;

        // On ne s'interesse qu'aux contrôleurs étendant AjaxController
        if(! class_exists($controllerClassName) or ! is_subclass_of($controllerClassName, AjaxController::class))
        {
            return;
        }

        $environment = $this->kernel->getEnvironment();
        $isAjax = $request->isXmlHttpRequest();

        if($environment !== 'dev' and ! $isAjax)
        {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette page.');
        }
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