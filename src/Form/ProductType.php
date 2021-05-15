<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

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
                'choices' => ['Phones' => 'Phones', 'Notebooks' => 'Notebooks', 'Printers' => 'Printers']
            ])
            ->add('price', NumberType::class, [
                'invalid_message' => "price must be number",
                'scale' => 2,
                'constraints' => [new Positive()],
            ])
            ->add('description',TextareaType::class)
            ->add('productImage')
            ->add('availableAmount', IntegerType::class, [
                'required' => false,
                'empty_data' => 0,
                'constraints' => [new Positive()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
