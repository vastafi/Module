<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Regex;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = ['id' => 'name'];
        $choises = array_flip($categories);
        $builder
            ->add('code', TextType::class, [
                    'constraints' => [
                        new Regex('/[A][B]\d+/', 'Code must begin with AB'),
                    ],
                ]
            )
            ->add('name')
            ->add('category', ChoiceType::class, [
                'choices' => ['Phones' => 'Phones', 'Notebooks' => 'Notebooks', 'Printers' => 'Printers']
            ])
            ->add('price', NumberType::class, [
                'invalid_message' => "price must be number",
                'scale' => 2,
                'constraints' => [new Positive()],
            ])
            ->add('description',TextareaType::class,[
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Description can have maximum 50 characters',
                    ]),
                ],
            ])
            ->add('productImage')
            ->add('availableAmount', IntegerType::class, [
                'required' => false,
                'empty_data' => 0,
                'constraints' => [new PositiveOrZero()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
