<?php

/**
 * Contrôleur des pages d'erreur
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\{ 
    Request, 
    JsonResponse, 
    Response 
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use App\SiteService;

final class ErrorController extends BaseController
{
    /**
     * Page d'erreur
     * @param Request $request
     * @param \Throwable $exception
     * @param SiteService $siteService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/error', 
            name: 'error',
            methods: [ "GET" ]
        )
    ]
    public function index(
        Request $request, 
        \Throwable $exception,
        SiteService $siteService
    ) : Response
    {


        $siteService->setWithError(true);

        $statusCode = (method_exists($exception, 'getStatusCode')) ? $exception->getStatusCode() : $exception->getCode();
        // $errorMessage = $exception->getMessage();

        $allowedCodes = [ 401, 403, 404, 500, ];
        $displayingStatusCode = $statusCode;
		if(! in_array($displayingStatusCode, $allowedCodes))
		{
			$displayingStatusCode = 500;
		}
		
        $displayingMessage = match($statusCode) {
            401 => 'Vous devez être identifié pour accéder à cette page.',
            403 => 'Vous n\'êtes pas autorisé à accéder à cette page.',
            404 => 'Cette page n\'existe pas ou a été déplacé.',
            default => 'Une erreur s\'est produite.',
        };

        // $displayingMessage = $errorMessage;

        $displayingData = [
            'code' => $displayingStatusCode,
            'message' => $displayingMessage,
        ];

        if($request->isXmlHttpRequest())
        {
            return new JsonResponse([
                'status' => AjaxController::STATUS_ERROR,
                'data' => $displayingData,
            ], $statusCode);
        }

        $content = $this->renderView('layout/error.html.twig', $displayingData);
        return new Response($content, $statusCode);
    }

}
