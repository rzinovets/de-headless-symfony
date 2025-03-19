<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Entity\MenuTree;
use App\Repository\MenuRepository;
use App\Repository\MenuTreeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class MenuCrudController extends AbstractCrudController
{
    private const string MAIN_MENU_LABEL = "Main menu item";
    private const int MAIN_MENU_ID = 0;
    private const string PARENT_ID_KEY = 'parent_id';
    private const string MENU_KEY = 'Menu';
    private const string POST_METHOD = 'POST';

    /**
     * @param ManagerRegistry $registry
     * @param MenuRepository $menuRepository
     * @param MenuTreeRepository $menuTreeRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly MenuRepository $menuRepository,
        private readonly MenuTreeRepository $menuTreeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            NumberField::new('weight'),
            ChoiceField::new(self::PARENT_ID_KEY, 'Sub menu')->setChoices(
                $this->buildMenuOptions($this->createMenuTreeStructure())
            ),
            UrlField::new('url')
        ];
    }

    /**
     * @param array $menuTree
     * @return int[]
     */
    private function buildMenuOptions(array $menuTree): array
    {
        $options = [self::MAIN_MENU_LABEL => self::MAIN_MENU_ID];
        $this->flattenMenuTree($menuTree, $options);
        return $options;
    }

    /**
     * @param array $menuTree
     * @param array $options
     * @param string $prefix
     * @return void
     */
    private function flattenMenuTree(array $menuTree, array &$options, string $prefix = ''): void
    {
        foreach ($menuTree as $id => $menu) {
            $label = $prefix . $menu['title'];
            $options[$label] = $id;
            if (!empty($menu['children'])) {
                $this->flattenMenuTree($menu['children'], $options, $prefix . ' > ');
            }
        }
    }

    /**
     * @return array
     */
    private function createMenuTreeStructure(): array
    {
        $menuTreeCollection = $this->menuTreeRepository->findAll();
        $menuCollection = $this->menuRepository->findAll();

        $menuLabels = [];
        $menuWeight = [];
        foreach ($menuCollection as $menu) {
            $menuLabels[$menu->getId()] = $menu->getTitle();
            $menuWeight[$menu->getId()] = $menu->getWeight();
        }

        $menuData = [];
        foreach ($menuTreeCollection as $menuEntity) {
            $id = $menuEntity->getEntityId();
            $menuData[$id] = [
                'id' => $id,
                'title' => $menuLabels[$id] ?? '',
                'weight' => $menuWeight[$id] ?? 0,
                'parent_id' => $menuEntity->getParentId(),
                'children' => []
            ];
        }

        foreach ($menuData as $id => &$menu) {
            if ($menu['parent_id'] !== self::MAIN_MENU_ID && isset($menuData[$menu['parent_id']])) {
                $menuData[$menu['parent_id']]['children'][$id] =& $menu;
            }
        }

        return array_filter($menuData, fn($menu) => $menu['parent_id'] === self::MAIN_MENU_ID);
    }

    /**
     * @param AdminContext $context
     */
    public function new(AdminContext $context)
    {
        $menu = $context->getRequest()->get(self::MENU_KEY);
        $menuTree = $this->menuTreeRepository->find(self::PARENT_ID_KEY) ?? new MenuTree();

        if ($context->getRequest()->getMethod() === self::POST_METHOD) {
            $parentId = $menu[self::PARENT_ID_KEY];
            $menuTree->setParentId($parentId);
            unset($menu[self::PARENT_ID_KEY]);
            $context->getRequest()->request->remove(self::MENU_KEY);
            $context->getRequest()->request->set(self::MENU_KEY, $menu);
        }

        $result = parent::new($context);

        if ($context->getRequest()->getMethod() === self::POST_METHOD) {
            $entityId = $context->getEntity()->getInstance()->getId();
            $menuTree->setEntityId($entityId);
            $this->entityManager->persist($menuTree);
            $this->entityManager->flush();
        }

        return $result;
    }
}