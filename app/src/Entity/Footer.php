<?php

namespace App\Entity;

use App\Repository\FooterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: FooterRepository::class)]
class Footer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $column_footer = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3,minMessage: 'Title must be at least 3 characters')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Url(message: 'Value is not valid')]
    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColumnFooter(): ?int
    {
        return $this->column_footer;
    }

    public function setColumnFooter(int $column_footer): self
    {
        $this->column_footer = $column_footer;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
