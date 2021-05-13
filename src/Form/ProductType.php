<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = ['id' => 'name'];
        $choises = array_flip($categories);
        $builder
            ->add('code')
            ->add('name')
            ->add('category', ChoiceType::class, [
                'choices' => ['Telephones' => 'Telephones', 'Notebooks' => 'Notebooks', 'Printers' => 'Printers']
            ])
            ->add('price')
            ->add('description')
            ->add('productImage')
                  ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
