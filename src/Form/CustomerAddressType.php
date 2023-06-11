<?php

namespace App\Form;

use App\Entity\CustomerAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('type')
            //->add('name')
            //->add('firstname')
            //->add('phone')
            ->add('address', TextType::class , ['label' => 'Street'])
            ->add('postalcode')
            ->add('city')
            ->add('country')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerAddress::class,
        ]);
    }
}
