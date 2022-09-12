<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2,max: 50)]
    private ?string $fullName = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email()]
    #[Assert\Length(min: 2,max: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2,max: 100)]
    private ?string $subjet = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdat = null;

    public function __construct()
    {
        $this->creatdat = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubjet(): ?string
    {
        return $this->subjet;
    }

    public function setSubjet(?string $subjet): self
    {
        $this->subjet = $subjet;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeImmutable
    {
        return $this->createdat;
    }

    public function setCreatedat(?\DateTimeImmutable $createdat): self
    {
        $this->createdat = $createdat;

        return $this;
    }
}
