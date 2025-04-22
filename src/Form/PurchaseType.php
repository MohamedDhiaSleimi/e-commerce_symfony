<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shippingAddress', TextareaType::class, [
                'label' => 'Shipping Address',
                'required' => true,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Enter your full shipping address'
                ]
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'Payment Method',
                'choices' => [
                    'Credit Card' => 'credit_card',
                    'PayPal' => 'paypal',
                    'Bank Transfer' => 'bank_transfer'
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}