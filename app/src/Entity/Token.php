<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\Column(name: "account_id", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\OneToOne(inversedBy: "Token", targetEntity: "Account")]
    #[ORM\JoinColumn(name: "account_id", referencedColumnName: "id")]
    private ?int $account_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $account = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?int $expired_time = null;

    public function getId(): ?int
    {
        return $this->account_id;
    }

    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    public function getAccount(): ?User
    {
        return $this->account;
    }

    public function setAccount(User $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiredTime(): ?int
    {
        return $this->expired_time;
    }

    public function setExpiredTime(int $expired_time): self
    {
        $this->expired_time = $expired_time;

        return $this;
    }
}
