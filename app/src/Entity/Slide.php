<?php

namespace App\Entity;

use App\Repository\SlideRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SlideRepository::class)]
class Slide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(nullable: true)]
    private ?int $slider_id;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    private ?string $description;

    #[ORM\Column]
    private ?bool $is_enabled = null;

    #[ORM\ManyToOne(targetEntity: Slider::class, inversedBy: 'slides')]
    #[ORM\JoinColumn(name: "slider_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Slider $slider;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $title;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $buttonUrl;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $buttonTitle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getSliderId(): ?int
    {
        return $this->slider_id;
    }

    public function setSliderId(int $slider_id): self
    {
        $this->slider_id = $slider_id;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $_ENV['MEDIA_URL'] . $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlider(): ?Slider
    {
        return $this->slider;
    }

    public function setSlider(Slider $slider): self
    {
        $this->slider = $slider;

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
    /**
     * @return string|null
     */
    public function getButtonUrl(): ?string
    {
        return $this->buttonUrl;
    }
    /**
     * @param string|null $buttonUrl
     */
    public function setButtonUrl(?string $buttonUrl): void
    {
        $this->buttonUrl = $buttonUrl;
    }
    /**
     * @return string|null
     */
    public function getButtonTitle(): ?string
    {
        return $this->buttonTitle;
    }
    /**
     * @param string|null $buttonTitle
     */
    public function setButtonTitle(?string $buttonTitle): void
    {
        $this->buttonTitle = $buttonTitle;
    }

    /**
     * @return bool|null
     */
    public function getIsEnabled(): ?bool
    {
        return $this->is_enabled;
    }

    /**
     * @param bool|null $is_enabled
     */
    public function setIsEnabled(?bool $is_enabled): void
    {
        $this->is_enabled = $is_enabled;
    }
}
