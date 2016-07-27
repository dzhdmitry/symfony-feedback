<?php

namespace AppBundle\Form;

use AppBundle\Entity\Picture;
use AppBundle\Form\Type\UploadingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('originalFilename', UploadingType::class, [
                'required' => false,
                'label' => "form.picture",
                'translation_domain' => "messages",
                'attr' => [
                    'accept' => "image/*"
                ],
                'constraints' => [
                    new Image([
                        'maxSize' => "5M",
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
