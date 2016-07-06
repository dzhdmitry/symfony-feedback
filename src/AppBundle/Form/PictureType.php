<?php

namespace AppBundle\Form;

use AppBundle\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('originalFilename', FileType::class, [
                'required' => false,
                'label' => "Picture",
                'constraints' => [
                    new Image([
                        'mimeTypes' => ["image/jpeg", "image/pjpeg", "image/png", "image/gif"]
                    ])
                ]
            ])
            ->addModelTransformer(new CallbackTransformer(function($value) {
                return $value;
            }, function($value) {
                if ($value instanceof Picture) {
                    if ($value->getOriginalFilename() == null) {
                        return null;
                    }
                }

                return $value;
            }));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class
        ]);
    }
}
