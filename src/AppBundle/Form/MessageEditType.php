<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class MessageEditType extends BaseMessageType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('approved', null, [
                'required' => false,
                'label' => "form.approved",
                'translation_domain' => "messages",
            ]);
    }
}
