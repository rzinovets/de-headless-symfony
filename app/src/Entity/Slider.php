<?php

namespace App\Entity;

use App\Repository\SliderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SliderRepository::class)]
class Slider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column]
    private ?string $slider_key = null;

    #[ORM\OneToMany(mappedBy: "slider", targetEntity: "Slide")]
    private $slides;

    public function __construct()
    {
        $this->slides = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
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

    public function getSlides(): Collection
    {
        return $this->slides;
    }

    /**
     * @return string|null
     */
    public function getSliderKey(): ?string
    {
        return $this->slider_key;
    }

    /**
     * @param string|null $slider_key
     */
    public function setSliderKey(?string $slider_key): void
    {
        $this->slider_key = $slider_key;
    }
}
