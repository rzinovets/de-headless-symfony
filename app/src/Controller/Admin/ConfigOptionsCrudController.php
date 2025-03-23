<?php

namespace App\Controller\Admin;

use App\Entity\ConfigOptions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigOptionsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConfigOptions::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('label_id'),
            TextField::new('text'),
        ];
    }
}
