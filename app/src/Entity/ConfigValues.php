<?php

namespace App\Entity;

use App\Repository\ConfigValuesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigValuesRepository::class)]
class ConfigValues
{
    #[ORM\Id]
    #[ORM\Column(name: 'code', type: 'string', unique: true, nullable: false)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
