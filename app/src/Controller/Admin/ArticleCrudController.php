<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Admin\Field;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ArticleCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string {
        return Article::class;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('title'),
            TextField::new('url_key'),
            BooleanField::new('is_enabled', 'Enabled'),
            TextEditorField::new('short_description'),
            Field\TinyField::new('description')
                ->hideOnIndex()
                ->addUploadContentUrl(
                    $this->container
                        ->get(AdminUrlGenerator::class)
                )
        ];
    }

    /**
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets {
        return $assets
            ->addJsFile('/tinymce/tinymce.js');
    }

    /**
     * @param string $entityFqcn
     * @return Article
     */
    public function createEntity(string $entityFqcn): Article {
        $entity = new Article();
        $entity->setIsEnabled(true);

        return $entity;
    }
}
