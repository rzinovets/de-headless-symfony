<?php

namespace App\Services\Menu;

use App\Repository\MenuRepository;
use App\Repository\MenuTreeRepository;

class MenuTree
{
    /**
     * @param MenuRepository $menuRepository
     * @param MenuTreeRepository $menuTreeRepository
     */
    public function __construct(
        private readonly MenuRepository $menuRepository,
        private readonly MenuTreeRepository $menuTreeRepository,
    ) {}

    /**
     * @return array
     */
    public function createArrayOfMenuTree(): array
    {
        $menuCollection = $this->menuRepository->findAll();
        $menuTreeCollection = $this->menuTreeRepository->findAll();

        $menuLabels = array_column($menuCollection, 'title', 'id');
        $menuWeight = array_column($menuCollection, 'weight', 'id');
        $menuUrl = array_column($menuCollection, 'url', 'id');

        $menuData = [];
        foreach ($menuTreeCollection as $menuEntity) {
            $id = $menuEntity->getEntityId();
            $menuData[$id] = [
                'id' => $id,
                'weight' => $menuWeight[$id] ?? 0,
                'parent_id' => $menuEntity->getParentId(),
                'title' => $menuLabels[$id] ?? "",
                'url' => $menuUrl[$id] ?? "",
            ];
        }

        uasort($menuData, fn($a, $b) => $a['weight'] <=> $b['weight']);

        return $menuData;
    }
}
