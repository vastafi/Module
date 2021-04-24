<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = ['id' => 'name'];
        $choises = array_flip($categories);
        # @fixme this form can be modified with field types (for example TextType)
        $builder
            ->add('code')
            ->add('name')
            ->add('category', ChoiceType::class, [
                'choices' => ['Telefoane' => 1, 'Laptopuri' => 2,'Imprimante' => 2]
            ])
            ->add('price')
            ->add('description')
            ->add('productImage')
            //->add('createdAt')
            //->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
