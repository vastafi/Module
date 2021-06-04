<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', TextareaType::class)
            ->add('paymentDetails', ChoiceType::class,[
                'choices' => [
                    'Cash' => 'Cash',
                    'Credit Card' => 'Credit Card'
                ],
                'label' => 'Payment method'
            ])
            //Credit Card Details
//            ->add('creditCardCode',TextType::class )
//            ->add('cvv',NumberType::class)
//            ->add('expiresAt',DateType::class,[
//                'widget' => 'single_text',
//            ])

//            ->add('creditCardDetails',CreditCardDetailsType::class,[
//                'mapped'=> false,
//                'required' => false
//            ])
            ->add('status', ChoiceType::class,[
                'choices' =>[
                    'New' => 'New',
                    'In progress' => 'In progress',
                    'Sent' => 'Sent',
                    'Closed' => 'Closed',
                    'Canceled' => 'Canceled'
                ],
                'required' => false,
                'empty_data' => 'New',
                'label' => 'Status'

            ])
            ->add('shippingDetails', ShippingDetailsType::class)
            ->add('total')
        ;

        $builder->get('paymentDetails')
            ->addModelTransformer(new CallbackTransformer(
                function ($paymentArray) {
                    return count($paymentArray) ? $paymentArray[0] :null;
                },
                function ($paymentArray) {
                    return [$paymentArray];
                }
            ));

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
//                function ($shippingDetailsArray) {
//                    return json_encode($shippingDetailsArray);
//                },
//                function ($shippingDetailsJson) {
//                    return json_decode($shippingDetailsJson);
//                }
//            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
