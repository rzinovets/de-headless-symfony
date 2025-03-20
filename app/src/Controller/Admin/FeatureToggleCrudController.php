<?php

namespace App\Controller\Admin;

use App\Entity\FeatureToggle;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FeatureToggleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FeatureToggle::class;
    }
}
