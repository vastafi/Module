<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class CreditCardDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creditCardCode',NumberType::class,[
                'label' => 'Code',
                'constraints' => [
                    new Length(16)
                ],
                'scale' => 0,
            ])
            ->add('cvv',NumberType::class,[
                'label' => 'CVV',
                'constraints' => [
                    new Length(3)
                ],
    ])
            ->add('expiresAt',TextType::class,[
                'constraints' => [
                    new Regex('/^(0[1-9]|1[0-2])\/?([0-9]{4}|[0-9]{2})$/','Enter a valid value'),
                ],
            ]);
    }

}