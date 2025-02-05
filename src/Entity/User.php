<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: ['username', 'email'],
    message: 'This user is already registered.',
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'The username cannot be empty')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[Assert\Email(
        message: 'The email is not a valid.',
    )]
    #[Assert\NotBlank(message: 'The email cannot be empty')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[Assert\Length(
        min: 8,
        max: 32,
        minMessage: 'Password is too small',
        maxMessage: 'Password is too long',
    )]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
