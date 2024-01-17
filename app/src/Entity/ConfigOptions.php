<?php

namespace App\Entity;

use App\Repository\ConfigOptionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigOptionsRepository::class)]
class ConfigOptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $label_id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: "ConfigLabels", cascade: ["remove"], inversedBy: "options")]
    #[ORM\JoinColumn(name: "label_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?ConfigLabels $labels = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelId(): ?int
    {
        return $this->label_id;
    }

    public function setLabelId(int $label_id): self
    {
        $this->label_id = $label_id;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getLabels(): ?ConfigLabels
    {
        return $this->labels;
    }

    public function setLabels(ConfigLabels $labels): self
    {
        $this->labels = $labels;

        return $this;
    }
}
