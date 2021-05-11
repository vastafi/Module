<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('items',TextareaType::class)
            ->add('paymentDetails', ChoiceType::class, [
                'choices' =>[
                    'Cash' => 'Cash',
                    'Credit Card' => 'Credit Card'
                    ],
                'label' => 'Payment method'
    ])
            ->add('status', ChoiceType::class,[
                'choices' =>[
                    'New' => 'New',
                    'In progress' => 'In progress',
                    'Sent' => 'Sent',
                    'Closed' => 'Closed',
                    'Canceled' => 'Canceled'
                ],
                'label' => 'Status'
            ])
            ->add('shippingDetails', TextareaType::class)
            ->add('total');

        $builder->get('paymentDetails')
            ->addModelTransformer(new CallbackTransformer(
                function ($paymentArray) {
                    return count($paymentArray) ? $paymentArray[0] :null;
                },
                function ($paymentArray) {
                    return [$paymentArray];
                }
            ));

//        $builder->get('items')
//            ->addModelTransformer(new CallbackTransformer(
//                function ($itemsArray) {
//                    return count($itemsArray) ? $itemsArray[0] :null;
//                },
//                function ($itemsArray) {
//                    return [$itemsArray];
//                }
//            ));
        $builder->get('items')
            ->addModelTransformer(new CallbackTransformer(
                function ($itemsArray) {
                    return json_encode($itemsArray);
                },
                function ($itemsJson) {
                    return json_decode($itemsJson);
                }
            ));
//        $builder->get('shippingDetails')
//            ->addModelTransformer(new CallbackTransformer(
//                function ($itemsArray) {
//                    return count($itemsArray) ? $itemsArray[0] :null;
//                },
//                function ($itemsArray) {
//                    return [$itemsArray];
//                }
//            ));
        $builder->get('shippingDetails')
            ->addModelTransformer(new CallbackTransformer(
                function ($shippingDetailsArray) {
                    return json_encode($shippingDetailsArray);
                },
                function ($shippingDetailsJson) {
                    return json_decode($shippingDetailsJson);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
