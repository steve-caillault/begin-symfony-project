<?php

/** 
 * Gestion générique du site (nom, titre de la page, section site ou admin notamment)
 */

namespace App;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\{ 
    Request, 
    RequestStack
    
};

final class SiteService {

    /**
     * Nom du site
     * @var string
     */
    private string $siteName;

    /**
     * Vrai si la maintenance est active
     * @var ?bool
     */
    private ?bool $maintenanceEnabled = null;

    /**
     * Vrai si on se trouve sur une page d'erreur
     * @var bool
     */
    private bool $withError = false;

    /**
     * Constructeur
     * @param ContainerBagInterface $configuration
     * @param RequestStack $requestStack
     */
    public function __construct(private ContainerBagInterface $configuration, private RequestStack $requestStack)
    {
        $this->siteName = $configuration->get('siteName');
    }
    
    /*********************************************/

    /**
     * Retourne si la maintenance est activée
     * @return bool
     */
    public function getMaintenanceEnabled() : bool
    {
        if($this->maintenanceEnabled === null)
        {
            $filePath = $this->configuration->get('maintenanceFilePath');
            $this->maintenanceEnabled = file_exists($filePath);
        }
        return $this->maintenanceEnabled;
    }

    /**
     * Retourne si on se trouve sur une page d'erreur
     * @return bool
     */
    public function getWithError() : bool
    {
        return $this->withError;
    }

    /**
     * Modifie si on se trouve sur une page d'erreur
     * @param bool $withError
     * @return self
     */
    public function setWithError(bool $withError) : self
    {
        $this->withError = $withError;
        return $this;
    }

    /**
     * Retourne si on se trouve dans la section d'administration
     * @return bool
     */
    public function isAdminSection() : bool
    {
        $routeName = $this->getRequest()?->attributes->get('_route');
        return (str_contains($routeName, 'admin_'));
    }

    /**
     * Retourne si on se trouve sur une page d'authentification (login)
     * @return bool
     */
    public function isAuthSection() : bool
    {
        $routeName = $this->getRequest()?->attributes->get('_route');
        return (str_contains($routeName, '_auth_'));
    }

    /**
     * Retourne le nom du site (à définir dans le fichier de configuration services.yaml : parameters.siteName)
     * @return ?string
     */
    public function getName() : ?string
    {
        return $this->siteName;
    }

    /**
     * Retourne la requête courante
     * @return ?Request
     */
    private function getRequest() : ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

}