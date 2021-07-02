<?php

/**
 * Entité représentant un utilisateur
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
/***/
use App\Repository\UserRepository;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ORM\Table(name: 'users'),
    ORM\UniqueConstraint(name: 'idx_public_id', columns: [ 'public_id' ]),
    UniqueEntity(fields: 'public_id', message: 'Un utilisateur utilise déjà cet identifiant.')
]
final class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    public const PERMISSION_ADMIN = 'ADMIN';

    /**
     * Identifiant
     * @var int
     */
    #[
        ORM\Id(),
        ORM\GeneratedValue(),
        ORM\Column(type: 'integer',  columnDefinition: 'SMALLINT(5) UNSIGNED AUTO_INCREMENT'),
    ]
    private int $id;

    /**
     * Identifiant public
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 50),
        Constraints\NotBlank(message: "L'identifiant de l'utilisateur est nécessaire."),
        Constraints\Length(
            min: 3, 
            max: 50, 
            minMessage: "L'identifiant doit avoir au moins {{ limit }} caractères.",
            maxMessage: "L'identifiant ne doit pas avoir plus de {{ limit }} caractères."

        )
    ]
    private string $public_id;

    /**
     * Prénom
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        Constraints\NotBlank(message: "Le prénom de l'utilisateur est nécessaire."),
        Constraints\Length(
            min: 3, 
            max: 100, 
            minMessage: "Le prénom doit avoir au moins {{ limit }} caractères.",
            maxMessage: "Le prénom ne doit pas avoir plus de {{ limit }} caractères."
        )
    ]
    private string $first_name;

    /**
     * Nom
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        Constraints\NotBlank(message: "Le nom de l'utilisateur est nécessaire."),
        Constraints\Length(
            min: 3, 
            max: 100, 
            minMessage: "Le nom doit avoir au moins {{ limit }} caractères.",
            maxMessage: "Le nom ne doit pas avoir plus de {{ limit }} caractères."
        )
    ]
    private string $last_name;

    /**
     * Mot de passe non crypté (n'est utilisé que pour les tests)
     * @var string
     */
    private ?string $test_password = null;

    /**
     * Mot de passe crypté
     * @ORM\Column(type="string", length=150)
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 150)
    ]
    private string $password_hashed;

    /**
     * Permissions
     * @var array
     */
    #[
        ORM\Column(type: 'json', columnDefinition: 'VARCHAR(250) NULL DEFAULT NULL'),
        Constraints\NotBlank(message: "Les permissions sont nécessaires."),
        Constraints\NotNull(message: "Les permissions sont nécessaires."),
    ]
    private array $permissions = [];

    /**
     * Constructeur
     */
    public function __construct()
    {
       
    }

    /*******************************************************/

    /* METHODE POUR L'AUTHENTIFICATION */

    /**
     * Retourne les rôles de l'utilisateur
     * @return string[]
     */
    public function getRoles()
    {
        $roles = $this->getPermissions();
        array_walk($roles, function(&$role) {
            $role = 'ROLE_' . strtoupper($role);
        });
        return $roles;
    }

    /**
     * Retourne le mot de passe crypté de l'utilisateur
     * @return ?string
     */
    public function getPassword() : ?string
    {
        return $this->getPasswordHashed();
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUserIdentifier()
    {
        return $this->getPublicId();
    }

    /**
     * @@inheritdoc
     */
    public function getUsername()
    {
        return $this->getUserIdentifier();
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        // $this->password_hashed = '';
    }

    /*******************************************************/

    /**
     * Retourne l'identifiant
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'identifiant public
     * @return string
     */
    public function getPublicId() : ?string
    {
        return $this->public_id;
    }

    /**
     * Modifie l'identifiant public
     * @param string $publicId
     * @return self
     */
    public function setPublicId(string $publicId) : self
    {
        $this->public_id = $publicId;
        return $this;
    }

    /**
     * Retourne le nom complet de l'utilisateur
     * @return string
     */
    public function getFullName() : string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Retourne le prénom
     * @return ?string
     */
    public function getFirstName() : ?string
    {
        return $this->first_name;
    }

    /**
     * Modifie le prénom
     * @param string $firstName
     * @return self
     */
    public function setFirstName(string $firstName) : self
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Retourne le nom
     * @return ?string
     */
    public function getLastName() : ?string
    {
        return $this->last_name;
    }

    /**
     * Modifie le nom
     * @param string $lastName
     * @return self
     */
    public function setLastName(string $lastName) : self
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Retourne le mot de passe crypté
     * @return ?string
     */
    public function getPasswordHashed() : ?string
    {
        return $this->password_hashed;
    }

    /**
     * Modifie le mot de passe crypté
     * @param string 
     * @return self
     */
    public function setPasswordHashed(string $passwordHashed) : self
    {
        $this->password_hashed = $passwordHashed;
        return $this;
    }

    /**
     * Retourne le mot de passe non crypté pour les tests
     * @return ?string
     */
    public function getTestPassword() : ?string
    {
        return $this->test_password;
    }

    /**
     * Modifie le mot de passe non crypté pour les tests
     * @param string $password
     * @return self
     */
    public function setTestPassword(string $password)
    {
        $this->test_password = $password;
        return $this;
    }

    /**
     * Retourne les permissions
     * @return ?array
     */
    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    /**
     * Retourne les permissions autorisées
     * @return array
     */
    private function getAllowedPermissions() : array
    {
        return [ self::PERMISSION_ADMIN, ];
    }

    /**
     * Ajoute une permission
     * @param string $permission
     * @return self
     */
    public function addPermission(string $permission) : self
    {
        $alreadyExistsPermission = in_array($permission, $this->permissions);

        if($permission !== '' and ! $alreadyExistsPermission)
        {
            array_push($this->permissions, $permission);
        }
        return $this;
    }

    /**
     * Validation des Permissions
     * @param ExecutionContextInterface $context
     * @return void
     */
    #[
        Constraints\Callback()
    ]
    public function arePermissionsValid(ExecutionContextInterface $context) : void
    {
        $allowedPermissions = $this->getAllowedPermissions();

        $forbiddenPermissions = array_filter($this->getPermissions(), fn($permission) => ! in_array($permission, $allowedPermissions));
        if(count($forbiddenPermissions) === 0)
        {
            return;
        }

        $message = 'Les permissions de l\'utilisateur sont incorrectes.';
        $context->buildViolation($message)->atPath('permissions')->addViolation();
    }

    /*******************************************************/

}
