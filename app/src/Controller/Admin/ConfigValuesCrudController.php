<?php

namespace App\Controller\Admin;

use App\Entity\ConfigValues;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigValuesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConfigValues::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code'),
            TextField::new('value'),
        ];
    }
}
