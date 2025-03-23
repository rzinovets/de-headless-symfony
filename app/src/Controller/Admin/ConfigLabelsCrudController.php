<?php

namespace App\Controller\Admin;

use App\Entity\ConfigLabels;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigLabelsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConfigLabels::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('groupId'),
            TextField::new('code'),
            TextField::new('label'),
            TextField::new('type'),
            BooleanField::new('isSecure'),
        ];
    }
}
