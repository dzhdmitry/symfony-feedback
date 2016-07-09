<?php

namespace AppBundle\Form;

use AppBundle\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', null, [
                'label' => "Name"
            ])
            ->add('email', EmailType::class)
            ->add('body', null, [
                'label' => "Message text",
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('picture', PictureType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'attr' => [
                'class' => "message-form"
            ]
        ]);
    }
}