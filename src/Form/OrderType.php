<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state')
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'name',
                'placeholder' => 'Selecciona un cliente',
            ])
            ->add('invoice', EntityType::class, [
                'class' => Invoice::class,
                'choice_label' => 'number',
                'placeholder' => 'Selecciona una factura'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
