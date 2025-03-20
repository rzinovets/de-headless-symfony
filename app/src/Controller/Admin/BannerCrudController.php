<?php

namespace App\Controller\Admin;

use App\Entity\Banner;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BannerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Banner::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('buttonTitle'),
            TextField::new('url'),
            ImageField::new('image')
                ->setUploadDir('public/media')
                ->setBasePath('media')
                ->setUploadedFileNamePattern(
                    fn(UploadedFile $file): string => preg_replace(
                        '/\s+/',
                        '_', $file->getClientOriginalName()
                    )
                ),
            BooleanField::new('isActive'),
        ];
    }
}
