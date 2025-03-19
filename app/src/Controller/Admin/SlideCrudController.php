<?php

namespace App\Controller\Admin;

use App\Entity\Slide;
use App\Repository\SliderRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SlideCrudController extends AbstractCrudController
{
    protected ?int $entityIdRequest = null;

    /**
     * @param ManagerRegistry $registry
     * @param AdminUrlGenerator $adminUrlGenerator
     * @param Request $request
     */
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly Request $request = new Request()
    ) {}

    /**
     * @return string
     */
    public static function getEntityFqcn(): string {
        return Slide::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('title'),
            TextField::new('description'),
            ImageField::new('image')
                ->setUploadDir("public/media")
                ->setBasePath('media')
                ->setUploadedFileNamePattern(
                    fn(UploadedFile $file): string => preg_replace(
                        '/\s+/',
                        '_', $file->getClientOriginalName()
                    )
                ),
            BooleanField::new('is_enabled', 'Enabled'),
            TextField::new('button_url'),
            TextField::new('button_title')
        ];
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions {
        $urlGenerator = $this->adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(SlideCrudController::class)
            ->setAction(Action::NEW);
        $urlGenerator->set('entitySliderId', $this->request->get('entitySliderId'));
        $newSlide = Action::new('createSlide', 'Create slide')
            ->linkToUrl($urlGenerator->generateUrl())
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $newSlide)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    /**
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('title')
            ->add('description')
            ->add('image');
    }

    /**
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function new(AdminContext $context): KeyValueStore|RedirectResponse|Response {
        $this->entityIdRequest = $context->getRequest()->request->get('entitySliderId');
        return parent::new($context);
    }

    /**
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function index(AdminContext $context): KeyValueStore|RedirectResponse|Response {
        $this->entityIdRequest = $context->getRequest()->request->get('entitySliderId');
        return parent::index($context);
    }

    /**
     * @return RedirectResponse
     */
    public function createSlide(): RedirectResponse {
        $urlGenerator = $this->adminUrlGenerator
            ->setController(SlideCrudController::class)
            ->setAction(Action::NEW);
        $urlGenerator->set('entitySliderId', $urlGenerator->get('entityId'))
            ->unset('entityId');

        return $this->redirect($urlGenerator->generateUrl());
    }

    /**
     * @param string $entityFqcn
     * @return mixed
     */
    public function createEntity(string $entityFqcn) {
        $slide = new $entityFqcn();
        $sliderRepository = new SliderRepository($this->registry);
        $slider = $sliderRepository->findBy(['id' => $this->entityIdRequest]);
//        $slide->setSlider($slider[0]);
        $slide->setIsEnabled(true);

        return $slide;
    }

    /**
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(
        SearchDto        $searchDto,
        EntityDto        $entityDto,
        FieldCollection  $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $filterValue = sprintf('%s.%s = :value', $queryBuilder->getAllAliases()[0], 'slider_id');
        $queryBuilder->andWhere($filterValue)
            ->setParameter('value', $this->entityIdRequest);
        return $queryBuilder;
    }
}
