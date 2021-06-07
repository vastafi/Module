<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutType extends AbstractType
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


            ->add('creditCardDetails',CreditCardDetailsType::class,[
                'mapped'=> true,
                'required' => false
            ])
            ->add('shippingDetails', ShippingDetailsType::class)
            ->add('total',NumberType::class,[
                'required' => false,
                'disabled' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Place Order',
                'attr' => ['class' => 'btn-success']
            ]);
        ;

//        $builder->get('paymentDetails')->addEventListener(
//            FormEvents::POST_SET_DATA,
//            function (FormEvent $event)
//            {
//                $form = $event->getForm();
//                $data = $event->getData();
//
//                if($form->getData()=='CreditCard') {
//                    $form->getParent()->add('creditCardDetails', CreditCardDetailsType::class, [
//                        'required' => 'false',
//                        'placeholder' => 'Credit Card Details'
//                    ]);
//                }
//            }
//        );

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
