<?php


namespace App\Form;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country',TextType::class)
            ->add('state',TextType::class)
            ->add('city',TextType::class)
            ->add('address1',TextType::class,[
                'required' => true
            ])
            ->add('address2',TextType::class,[
                'required' => false
            ])
        ;
    }

}