<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Entity\MenuTree;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class MenuCrudController extends AbstractCrudController
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

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ManagerRegistry        $registry,
        EntityManagerInterface $entityManager
    ) {
        $this->em = $registry;
        $this->menuRepository = $this->em->getRepository(Menu::class);
        $this->menuTreeRepository = $this->em->getRepository(MenuTree::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string {
        return Menu::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('title'),
            NumberField::new('weight'),
            ChoiceField::new('parent_id', 'Sub menu')->setChoices(
                $this->createArrayOfMenu($this->buildTree($this->createArrayOfMenuTree()))
            ),
            UrlField::new('url')
        ];
    }

    /**
     * @param $orderedArray
     * @param $level
     * @return int[]
     */
    public function createArrayOfMenu($orderedArray, $level = ""): array {
        $resultArray = [
            "Main menu item" => 0,
        ];

        foreach ($orderedArray as $key => $item) {
            try {
                $resultArray[$level . $item['title']] = $key;
            } catch (\Exception $e) {
            }

            if (isset($item['sub'])) {
                $childItems = $this->createArrayOfMenu($item['sub'], $level . "--");
                $resultArray = array_merge($resultArray, $childItems);
            }
        }

        return $resultArray;
    }

    /**
     * @return array
     */
    public function createArrayOfMenuTree(): array {
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
            $menuTreeEntityId = $menuEntity->getEntityId();
            $menuData[$menuTreeEntityId] = [
                'weight' => $menuWeight[$menuTreeEntityId] ?? 0,
                'parent_id' => $menuEntity->getParentId(),
                'title' => $menuLabels[$menuTreeEntityId] ?? ""
            ];
        }

        uasort($menuData, fn($a, $b) => $a['weight'] < $b['weight']);

        return $menuData;
    }

    /**
     * @param array $listIdParent
     * @return array
     */
    public function buildTree(array $listIdParent): array {
        foreach ($listIdParent as $id => $node) {
            $listIdParent[$node['parent_id']]['sub'][$id] =& $listIdParent[$id];
        }

        try {
            return $listIdParent[1]['sub'];
        } catch (\Exception $e) {
        }

        return $listIdParent;
    }

    /**
     * @param AdminContext $context
     * @return \EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(AdminContext $context) {
        $em = $this->entityManager;
        $menu = $context->getRequest()->get('Menu');
        $menuTree = $this->menuTreeRepository->find('parent_id');

        if ($menuTree === null) {
            $menuTree = new MenuTree();
        }

        if ($context->getRequest()->getMethod() == 'POST') {
            $parentId = $menu['parent_id'];
            $menuTree->setParentId($parentId);
            unset($menu['parent_id']);
            $context->getRequest()->request->remove('Menu');
            $context->getRequest()->request->set('Menu', $menu);
        }

        $result = parent::new($context);

        if ($context->getRequest()->getMethod() == 'POST') {
            $entityId = $context->getEntity()->getInstance()->getId();
            $menuTree->setEntityId($entityId);
            $em->persist($menuTree);
            $em->flush();
        }

        return $result;
    }
}