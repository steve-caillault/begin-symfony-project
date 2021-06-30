<?php

/**
 * Contrôleur de base pour les appels Ajax
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    RequestStack,
    JsonResponse
};
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AjaxController extends AbstractController {

    public const
        STATUS_SUCCESS = 'SUCCESS',
        STATUS_ERROR = 'ERROR'
    ;

    /**
     * Données retournées par l'appel Ajax
     * @var array
     */
    protected array $responseData = [
        'status' => self::STATUS_ERROR,
        'data' => null,
    ];

    /**********************************************/

    /**
     * Vérifie s'il s'agit d'une requête Ajax
     * @param RequestStack $requestStack
     * @param KernelInterface $kernel
     * @return void
     * @required
     */
    public function checkAjaxRequest(RequestStack $requestStack, KernelInterface $kernel)
    {
        $environment = $kernel->getEnvironment();
        $isAjax = $requestStack->getCurrentRequest()?->isXmlHttpRequest();

        if($environment !== 'dev' and ! $isAjax)
        {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette page.');
        }
    }

    /**
     * Retourne la réponse JSON à retourner
     * @param array $data
     * @param string $status
     * @return JsonResponse
     */
    protected function getAjaxResponse(array $data, string $status = self::STATUS_ERROR) : JsonResponse
    {
        $this->response_data = [
            'status' => $status,
            'data' => $data,
        ];

        $statusCode = ($status === self::STATUS_SUCCESS) ? 200 : 400;
        return new JsonResponse($this->response_data, $statusCode);
    }

    /**********************************************/

}