<?php

/**
 * Test sur la pagination
 */

namespace App\Tests;
 
use Twig\Environment as Twig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\UI\Pagination\Pagination;

final class PaginationTest extends BaseTestCase {
 
    /**
     * Test lorsque la pagination est vide
     * @return void
     */
    public function testEmpty() : void
    {
        $tests = [ 0, 13 ];

        foreach($tests as $totalItems)
        {
            $pagination = new Pagination(totalItems: $totalItems);
            $render = $this->getPaginationRender($pagination);
            $this->assertEquals('', $render);
        } 
    }

    /**
     * Test avec une pagination
     * @return void
     */
    public function testWithPages() : void
    {
        $tests = range(20, 200, 10);
        $itemsPerPage = 10;

        $paramNames = [ 
           Pagination::METHOD_QUERY => 'page',
           Pagination::METHOD_ROUTE => 'customPage',
        ];

        foreach($tests as $totalItems)
        {
            $totalPages = (int) ceil($totalItems / $itemsPerPage);

            // Pour chaque type de paramètre (query ou route)
            foreach($paramNames as $paramType => $paramName)
            {
                for($currentPage = 1 ; $currentPage <= $totalPages ; $currentPage++)
                {
                    $query = match($paramType) {
                        Pagination::METHOD_QUERY => [ $paramName => $currentPage ],
                        default => []
                    };

                    $routeParams = match($paramType) {
                        Pagination::METHOD_ROUTE => [
                            'param1' => 'value2',
                            $paramName => $currentPage,
                        ],
                        default => [
                            'param1' => 'value3',
                        ]
                    };

                    // Simule une requête pour générer les URL
                    $request = Request::create('testing/pagination')->duplicate(
                        query: $query,
                        attributes: array_merge($routeParams, [
                            '_route' => 'testing.pagination',
                        ])
                    );

                    $pagination = (new Pagination(itemsPerPage: $itemsPerPage, totalItems: $totalItems))
                        ->setPageParameterName($paramName)
                        ->setPageParameterType($paramType)
                        ->setRequest($request);
                    $render = $this->getPaginationRender($pagination);

                    $expected = $this->getPaginationRenderExpected(
                        $currentPage, 
                        $totalPages, 
                        $paramName, 
                        $request
                    );
                
                    $this->assertStringContainsString($expected, $render);
                }
            }
        }
    }

    /**
     * Retourne le rendu de la pagination
     * @return ?string
     */
    private function getPaginationRender(Pagination $pagination) : ?string
    {
        return $this->getService(Twig::class)->render('testing/ui/pagination.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Retourne le rendu de la pagination attendu
     * @param int $currentPage
     * @param int $totalPages
     * @param string $paramName
     * @param Request $request
     * @return string
     */
    private function getPaginationRenderExpected(
        int $currentPage, 
        int $totalPages, 
        string $paramName,
        Request $request
    ) : ?string 
    {
        if($totalPages < 2)
        {
            return null;
        }

        $urlGenerator = $this->getService(UrlGeneratorInterface::class);
    
        $query = $request->query->all();
        $routeParams = array_filter($request->attributes->all(), fn($key) => $key[0] != '_', ARRAY_FILTER_USE_KEY);

        $params = array_merge($query, $routeParams);

        $pages = [];
        for($i = 1 ; $i <= $totalPages ; $i++)
        {
            if($i === $currentPage)
            {
                $label = $i;
            }
            else
            {
                $label = $urlGenerator->generate('testing.pagination', array_merge($params, [
                    $paramName => $i,
                ]));
            }
            $pages[$i] = $label;
        }

        $elements = $pages;

        $min = 7; // Minimum de pages adjacentes à la page courante à afficher
        $muchPages = 11;
       
        if($totalPages > $muchPages)
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

        return $this->getService(Twig::class)->render('ui/pagination.html.twig', [
            'elements' => $elements,
            'current' => $currentPage,
        ]);
    }
}