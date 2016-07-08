<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Widget template: `uploading_widget` in @App/Form/fields.html.twig
 */
class UploadingType extends AbstractType
{
    public function getParent()
    {
        return FileType::class;
    }

    public function getName()
    {
        return "uploading";
    }

    public function getBlockPrefix()
    {
        return "uploading";
    }
}
