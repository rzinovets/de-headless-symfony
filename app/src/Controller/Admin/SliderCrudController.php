<?php

namespace App\Controller\Admin;

use App\Entity\Slider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SliderCrudController extends AbstractCrudController
{
    /**
     * @var AdminUrlGenerator
     */
    private $adminUrlGenerator;

    /**
     * @param AdminUrlGenerator $adminUrlGenerator
     */
    public function __construct(
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string {
        return Slider::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('title'),
            TextField::new('slider_key')
        ];
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions {
        $showSlides = Action::new('showSlides', 'Show slides')
            ->linkToCrudAction('showSlides');
        $newSlide = Action::new('createSlide', 'Create slide')
            ->linkToCrudAction('createSlide');

        return $actions
            ->add(Crud::PAGE_INDEX, $showSlides)
            ->add(Crud::PAGE_INDEX, $newSlide);
    }

    /**
     * @return RedirectResponse
     */
    public function showSlides(): RedirectResponse {
        $urlGenerator = $this->adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(SlideCrudController::class)
            ->setAction(Action::INDEX);
        $urlGenerator
            ->set('entitySliderId', $urlGenerator->get('entityId'))
            ->unset('entityId');

        return $this->redirect($urlGenerator->generateUrl());
    }

    /**
     * @return RedirectResponse
     */
    public function createSlide(): RedirectResponse {
        $urlGenerator = $this->adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(SlideCrudController::class)
            ->setAction(Action::NEW);
        $urlGenerator
            ->set('entitySliderId', $urlGenerator->get('entityId'))
            ->unset('entityId');

        return $this->redirect($urlGenerator->generateUrl());
    }
}
