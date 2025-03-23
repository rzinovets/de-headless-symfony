<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Article;
use App\Entity\Banner;
use App\Entity\Block;
use App\Entity\ConfigGroups;
use App\Entity\ConfigLabels;
use App\Entity\ConfigOptions;
use App\Entity\ConfigValues;
use App\Entity\ContactForm;
use App\Entity\FAQ;
use App\Entity\FeatureToggle;
use App\Entity\Footer;
use App\Entity\Menu;
use App\Entity\Page;
use App\Entity\Slider;
use App\Entity\User;
use Cron\CronBundle\Entity\CronJob;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Custom CMS')
            ->setFaviconPath('media/admin/admin.svg')
            ->setTextDirection('ltr')
            ->setLocales([
                'en' => '🇬🇧 English',
                'ru' => '🇷🇺 Russian',
            ]);
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('User Management');
        yield MenuItem::linkToCrud('Users', 'fa fa-user-circle-o', User::class);
        yield MenuItem::linkToCrud('Admin Users', 'fa fa-user-o', Admin::class);

        yield MenuItem::section('Content Management');
        yield MenuItem::linkToCrud('Menu', 'fa fa-list', Menu::class);
        yield MenuItem::linkToCrud('Slider', 'fa fa-sliders', Slider::class);
        yield MenuItem::linkToCrud('Footer', 'fa fa-table', Footer::class);
        yield MenuItem::linkToCrud('Articles', 'fa fa-file-text', Article::class);
        yield MenuItem::linkToCrud('Pages', 'fa fa-list-alt', Page::class);
        yield MenuItem::linkToCrud('Blocks', 'fa fa-picture-o', Block::class);
        yield MenuItem::linkToCrud('Banner', 'fa fa-bullhorn', Banner::class);
        yield MenuItem::linkToCrud('FAQ', 'fa fa-question-circle-o', FAQ::class);

        yield MenuItem::section('Configuration');
        yield MenuItem::linkToRoute('Configuration Overview', 'fa fa-diamond', 'admin_config')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::subMenu('Create Configuration', 'fa fa-cog')->setSubItems([
            MenuItem::linkToCrud('Config Groups', 'fa fa-object-group', ConfigGroups::class),
            MenuItem::linkToCrud('Config Labels', 'fa fa-tags', ConfigLabels::class),
            MenuItem::linkToCrud('Config Values', 'fa fa-list-ol', ConfigValues::class),
            MenuItem::linkToCrud('Config Options', 'fa fa-sliders-h', ConfigOptions::class),
        ]);

        yield MenuItem::section('System Tools');
        yield MenuItem::linkToCrud('Contact Form', 'fa fa-address-book', ContactForm::class);
        yield MenuItem::linkToCrud('Cron Jobs', 'fa fa-crown', CronJob::class);
        yield MenuItem::linkToCrud('Feature Toggles', 'fa fa-toggle-on', FeatureToggle::class);

        yield MenuItem::section('Exit');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}
