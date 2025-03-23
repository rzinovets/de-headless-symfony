<?php

namespace App\Controller\Admin;

use App\Entity\ConfigGroups;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigGroupsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConfigGroups::class;
    }
}
