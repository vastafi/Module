<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag', TextType::class, [
                'attr' => array(
                    'placeholder' => 'Separate tags'
                )
            ])

            ->add('path', FileType::class, [
                'required' => false,
                'empty_data' => '% & # { } \\ / ! $ \' \" : < > @  * ? + ` | =',
                'data_class' => null,
                'attr' =>  ['accept' => ".png,.jpg,.jpeg"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}