<?php
declare(strict_types=1);

// Entity class for the "User" table (no SQL here, see UserController)
class User
{
    private ?int $idUser;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $password;
    private string $role;
    private string $statut;

    public function __construct(
        ?int $idUser,
        string $nom,
        string $prenom,
        string $email,
        string $password,
        string $role = 'agriculteur',
        string $statut = 'actif'
    ) {
        $this->idUser = $idUser;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->statut = $statut;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }
}
