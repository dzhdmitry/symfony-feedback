<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageCreateType extends BaseMessageType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('author', null, [
                'label' => "form.author",
                'translation_domain' => "messages"
            ])
            ->add('email', EmailType::class, [
                'label' => "form.email",
                'translation_domain' => "messages"
            ])
            ->add('picture', PictureType::class);
    }
}
