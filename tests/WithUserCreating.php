<?php

/**
 * Trait pour la création d'utilisateur
 */

namespace App\Tests;

use Psr\Log\LoggerInterface;
/***/
use App\Entity\User;

trait WithUserCreating {

    use WithUserGenerating;
    
    /**
     * Retourne un utilisateur à loguer
     * @return User
     */
    private function userToLogged() : User
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $user = $generatorUser->current();

        try {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush();
        } catch(\Exception $e) {
            $this->getService(LoggerInterface::class)->debug($e->getMessage());
        }

        return $user;
    }

}