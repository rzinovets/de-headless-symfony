<?php

namespace App\Model;

use App\Entity\Menu;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

class MenuTree
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $em;
    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $menuRepository;
    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $menuTreeRepository;


    public function __construct(
        ManagerRegistry        $registry,
    )
    {
        $this->em = $registry;
        $this->menuRepository = $this->em->getRepository(Menu::class);
        $this->menuTreeRepository = $this->em->getRepository(\App\Entity\MenuTree::class);
    }

    public function createArrayOfMenuTree(): array
    {
        $menuTreeCollection = $this->menuTreeRepository->findAll();
        $menuCollection = $this->menuRepository->findAll();

        $menuLabels = [];
        $menuWeight = [];
        $menuUrl = [];

        foreach ($menuCollection as $menu) {
            $menuLabels[$menu->getId()] = $menu->getTitle();
            $menuWeight[$menu->getId()] = $menu->getWeight();
            $menuUrl[$menu->getId()] = $menu->getUrl();
        }

        $menuData = [];

        foreach ($menuTreeCollection as $menuEntity) {
            $menuTreeEntityId = $menuEntity->getEntityId();
            $menuData[$menuTreeEntityId] = [
                'id' => $menuEntity->getEntityId(),
                'weight' => $menuWeight[$menuTreeEntityId] ?? 0,
                'parent_id' => $menuEntity->getParentId(),
                'title' => $menuLabels[$menuTreeEntityId] ?? "",
                'url' => $menuUrl[$menuTreeEntityId] ?? "",
            ];
        }

        uasort($menuData, fn($a, $b) => $a['weight'] < $b['weight']);

        return $menuData;
    }
}