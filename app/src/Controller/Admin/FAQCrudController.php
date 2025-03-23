<?php

namespace App\Controller\Admin;

use App\Entity\FAQ;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FAQCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FAQ::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('question'),
            TextEditorField::new('answer'),
            NumberField::new('priority'),
        ];
    }
}
