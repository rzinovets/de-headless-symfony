<?php

namespace App\Controller\Admin;

use App\Entity\Footer;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FooterCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string {
        return Footer::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable {
        return [
            NumberField::new('column_footer', 'Column'),
            NumberField::new('position'),
            TextField::new('title'),
            TextField::new('value'),
            ChoiceField::new('type')->setChoices([
                'block' => '1',
                'url' => '2',
            ]),
        ];
    }
}
