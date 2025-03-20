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

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly Request $request = new Request()
    ) {}

    public static function getEntityFqcn(): string {
        return Slide::class;
    }

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

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('title')
            ->add('description')
            ->add('image');
    }

    public function new(AdminContext $context): KeyValueStore|RedirectResponse|Response {
        $this->entityIdRequest = $context->getRequest()->get('entitySliderId');
        return parent::new($context);
    }

    public function index(AdminContext $context): KeyValueStore|RedirectResponse|Response {
        $this->entityIdRequest = $context->getRequest()->get('entitySliderId');
        return parent::index($context);
    }

    public function createSlide(): RedirectResponse {
        $urlGenerator = $this->adminUrlGenerator
            ->setController(SlideCrudController::class)
            ->setAction(Action::NEW);
        $urlGenerator->set('entitySliderId', $urlGenerator->get('entityId'))
            ->unset('entityId');

        return $this->redirect($urlGenerator->generateUrl());
    }

    public function createEntity(string $entityFqcn) {
        $slide = new $entityFqcn();
        $sliderRepository = new SliderRepository($this->registry);
        $slider = $sliderRepository->find($this->entityIdRequest);

        if ($slider) {
            $slide->setSlider($slider);
        }

        $slide->setIsEnabled(true);

        return $slide;
    }

    public function createIndexQueryBuilder(
        SearchDto        $searchDto,
        EntityDto        $entityDto,
        FieldCollection  $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->entityIdRequest) {
            $filterValue = sprintf('%s.%s = :value', $queryBuilder->getAllAliases()[0], 'slider_id');
            $queryBuilder->andWhere($filterValue)
                ->setParameter('value', $this->entityIdRequest);
        }

        return $queryBuilder;
    }
}