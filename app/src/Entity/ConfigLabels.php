<?php

namespace App\Entity;

use App\Repository\ConfigLabelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigLabelsRepository::class)]
class ConfigLabels
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $group_id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: 'boolean')]
    private ?string $is_secure = null;

    #[ORM\OneToMany(mappedBy: "labels", targetEntity: "ConfigOptions")]
    private $options;

    #[ORM\ManyToOne(targetEntity: "ConfigGroups", cascade: ["remove"], inversedBy: "labels")]
    #[ORM\JoinColumn(name: "group_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?ConfigGroups $groups = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupId(): ?int
    {
        return $this->group_id;
    }

    public function setGroupId(int $group_id): self
    {
        $this->group_id = $group_id;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCroups(): ?ConfigGroups
    {
        return $this->groups;
    }

    public function setGroups(ConfigGroups $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getIsSecure(): ?bool
    {
        return $this->is_secure;
    }

    public function setIsSecure(bool $is_secure): self
    {
        $this->is_secure = $is_secure;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(?ConfigOptions $options): self
    {
        $this->options = $options;

        return $this;
    }
}
