<?php

/**
 * Contrôleur des pages d'erreur
 */

namespace App\Controller;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\{ 
    Request,
    Response 
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;

final class ErrorController extends BaseController
{
    /**
     * Page d'erreur
     * @param Request $request
     * @param \Throwable $exception
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/error',
            methods: [ "GET" ]
        )
    ]
    public function index(
        Request $request, 
        \Throwable $exception,
        TranslatorInterface $translator
    ) : Response
    {
        $statusCode = (method_exists($exception, 'getStatusCode')) ? $exception->getStatusCode() : $exception->getCode();
        // $errorMessage = $exception->getMessage();

        $allowedCodes = [ 401, 403, 404, 500, ];
        $displayingStatusCode = $statusCode;
		if(! in_array($displayingStatusCode, $allowedCodes))
		{
			$displayingStatusCode = 500;
		}
        
        $displayingMessage = match($statusCode) {
            401 => 'errors.unauthorized',
            403 => 'errors.denied',
            404 => 'errors.not_found',
            default => 'errors.default',
        };

        // $displayingMessage = $errorMessage;

        $displayingData = [
            'code' => $displayingStatusCode,
            'message' => $translator->trans($displayingMessage),
        ];

        if($request->isXmlHttpRequest())
        {
            return $this->json([
                'status' => AjaxController::STATUS_ERROR,
                'data' => $displayingData,
            ], $statusCode);
        }

        return $this->render('layout/error.html.twig', $displayingData)
            ->setStatusCode($statusCode);
    }

}
