<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Article;
use App\Entity\Block;
use App\Entity\Footer;
use App\Entity\Menu;
use App\Entity\Slider;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Locale;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard {
        return Dashboard::new()
            ->setTitle('Redepy')
            ->setFaviconPath('favicon.svg')
            ->setTextDirection('ltr')
            ->setLocales(['en', 'ru'])
            ->setLocales([
                'en' => '🇬🇧 English',
                'ru' => '🇷🇺 Russian'
            ])
            ->setLocales([
                'en', // locale without custom options
                Locale::new('ru', 'russian', 'far fa-language')
            ]);

    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('User', 'fa fa-user-circle-o', User::class);
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('Admin', 'fa fa-user-o', Admin::class);
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('Menu', 'fa fa-list', Menu::class);
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('Slider', 'fa fa-sliders', Slider::class);
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('Footer', 'fa fa-list-alt', Footer::class);
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('Articles', 'fa fa-file-text', Article::class);
        yield MenuItem::section('');
        yield MenuItem::linkToRoute('Configuration', 'fa fa-diamond', 'admin_config')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('Blocks', 'fa fa-picture-o', Block::class);
        yield MenuItem::section('');
        yield MenuItem::section('Exit');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}
