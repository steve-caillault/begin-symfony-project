<?php

/**
 * Service pour le rendu de la pagination
 */

namespace App\UI\Pagination;

use Twig\Environment as Twig;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\{
    Request, 
    RequestStack
};

final class PaginationService {

    /**
     * Requête courante
     * @var Request
     */
    private Request $request;

    /**
     * Constructeur
     * @param Twig $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     */
    public function __construct(
        private Twig $twig, 
        private UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    )
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Retourne le rendu de la pagination
     * @param Pagination $pagination
     * @return ?string
     */
    public function getRender(Pagination $pagination) : ?string
    {
        $totalPages = $pagination->getTotalPages();
        if($totalPages < 2)
        {
            return null;
        }
        $currentPage = $this->getCurrentPage($pagination);
     
        $pages = [];
		for($i = 1 ; $i <= $totalPages ; $i++)
		{
			$pages[$i] = $this->getPageUrl($pagination, $i);
		}

        $elements = $pages;

        $min = 7; // Minimum de pages adjacentes à la page courante à afficher
        $muchPages = 11;
       
        if(count($pages) > $muchPages)
        {
            $elements = [];
             
            if($currentPage < $min)
            {
                $elements = 
                   array_slice($pages, 0, $min, preserve_keys: true) + 
                   [ 'blank1' => '...' ] + 
                   array_slice($pages, $totalPages - 2, preserve_keys: true)
                ;
            }
            elseif($currentPage >= $min and $currentPage <= $totalPages - $min + 1)
            {
                $elements = 
                   array_slice($pages, 0, 2, preserve_keys: true) + 
                   [ 'blank1' => '...' ] + 
                   array_slice($pages, $currentPage - ceil($min / 2), $min, preserve_keys: true) +
                   [ 'blank2' => '...' ] + 
                    array_slice($pages, $totalPages - 2, preserve_keys: true)
                ;
            }
            else
            {
                $elements = 
                    array_slice($pages, 0, 2, preserve_keys: true) +
                    [ 'blank1' => '...' ] +
                    array_slice($pages, $totalPages - $min, preserve_keys: true)
                ;
            }
        }


        return $this->twig->render('ui/pagination.html.twig', [
            'elements' => $elements,
            'pages' => $pages,
            'current' => $currentPage,
            'total' => $totalPages,
        ]);
    }

    /**
     * Retourne le numéro de la page courante pour la pagination
     * @param Pagination $pagination
     * @return int
     */
    private function getCurrentPage(Pagination $pagination) : int
    {
        $parameterType = $pagination->getPageParameterType();
        $parameterName = $pagination->getPageParameterName();

        $currentParams = match($parameterType) {
            Pagination::METHOD_QUERY => $this->request?->query->all() ?? [],
            Pagination::METHOD_ROUTE => $this->request?->attributes->all() ?? [],
        };

        $pageParam = (int) ($currentParams[$parameterName] ?? 1);

        // Correctif si la page courante est supérieur au nombre de page
        $totalPage = $pagination->getTotalPages();
        
        $page = min($pageParam, $totalPage); 

        return $page;
    }

    /**
     * Retourne l'URL de la page en paramètre
     * @param Pagination $pagination
     * @param int $page
     * @return ?string
     */
    private function getPageUrl(Pagination $pagination, int $page) : ?string
    {
		$pages = range(1, $pagination->getTotalPages());
		if(! in_array($page, $pages))
		{
			return null;
		}
		
        $requestRouteParams = $this->request?->attributes->all() ?? [];
        $requestQueryParams = $this->request?->query->all() ?? [];

        // Récupération des paramètres de la route et GET actuel
		$routeParams = array_filter($requestRouteParams, fn($key) => (($key[0] ?? '') != '_'), ARRAY_FILTER_USE_KEY);
		$queryParams = array_filter($requestQueryParams, fn($key) => (($key[0] ?? '') != '_'), ARRAY_FILTER_USE_KEY);
        
        // Affecte le numéro de page à la route ou au paramètre GET
        $method = strtolower($pagination->getPageParameterType());
        $pageParamName = $pagination->getPageParameterName();
        ${ $method . 'Params' }[$pageParamName] = $page;
		
		$routeName = $this->request?->attributes->get('_route');
		$uri = $this->urlGenerator->generate($routeName, $routeParams);

		if(count($queryParams) > 0)
		{
			$uri .= '?' . http_build_query($queryParams);
        }
		
		return $uri;
    }

}