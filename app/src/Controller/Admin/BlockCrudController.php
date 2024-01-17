<?php

namespace App\Controller\Admin;

use App\Entity\Block;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BlockCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string {
        return Block::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('title'),
            TextField::new('identifier'),
            TextField::new('content'),
            ImageField::new('image')
                ->setUploadDir("public/media")
                ->setBasePath('media')
                ->setUploadedFileNamePattern(
                    fn(UploadedFile $file): string => preg_replace(
                        '/\s+/',
                        '_', $file->getClientOriginalName()
                    )
                ),
            BooleanField::new('is_active', 'Enabled'),
        ];
    }
}
