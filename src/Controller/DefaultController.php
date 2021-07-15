<?php

/**
 * Contrôleur d'index
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DefaultController extends BaseController
{

    /**
     * Page d'index
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/'
        )
    ]
    public function index(ValidatorInterface $validator) : Response
    {
        return $this->render('layout/base.html.twig');
    }
}