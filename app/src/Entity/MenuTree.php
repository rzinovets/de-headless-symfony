<?php

namespace App\Entity;

use App\Repository\MenuTreeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuTreeRepository::class)]
class MenuTree
{
    #[ORM\Id]
    #[ORM\Column(name: 'entity_id', type: 'integer', nullable: false)]
    #[ORM\OneToOne(inversedBy: "MenuTree", targetEntity: "Menu")]
    #[ORM\JoinColumn(name: "entity_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?int $entity_id = null;

    #[ORM\Column]
    private ?int $parent_id = null;

    public function getEntityId(): ?int
    {
        return $this->entity_id;
    }

    public function setEntityId(int $entity_id): self
    {
        $this->entity_id = $entity_id;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(int $parent_id): self
    {
        $this->parent_id = $parent_id;

        return $this;
    }
}
