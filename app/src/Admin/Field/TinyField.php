<?php

namespace App\Admin\Field;

use App\Controller\Admin\UploadImageController;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

final class TinyField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->addJsFiles('/tinymce/tinymceInit.js')
            ;
    }

    /**
     * @param AdminUrlGenerator $urlGenerator
     * @return $this
     */
    public function addUploadContentUrl(AdminUrlGenerator $urlGenerator)
    {
        $url = $urlGenerator->setAction('upload')
            ->setController(UploadImageController::class)
            ->generateUrl();
        $this->addHtmlContentsToBody('<script>window.tinymce_upload_image_url = "' . $url . '" </script>');
        return $this;
    }
}
