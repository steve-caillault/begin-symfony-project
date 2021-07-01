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
     * Requête HTTP
     * @var Request
     */
    private ?Request $request;

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
    public function __construct(private ContainerBagInterface $configuration, RequestStack $requestStack)
    {
        $this->siteName = $configuration->get('siteName');
        $this->request = $requestStack->getCurrentRequest();
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
     * @param bool $error
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
        $routeName = $this->request?->attributes->get('_route');
        return (strpos($routeName, 'admin_') !== false);
    }

    /**
     * Retourne si on se trouve sur une page d'authentification (login)
     * @return bool
     */
    public function isAuthSection() : bool
    {
        $routeName = $this->request?->attributes->get('_route');
        return (strpos($routeName, '_auth_') !== false);
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
     * Modification de la requête
     * @param Request $request
     * @retur self
     */
    public function setRequest(Request $request) : self
    {
        $this->request = $request;
        return $this;
    }

}