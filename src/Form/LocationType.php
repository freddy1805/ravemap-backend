<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'location.name',
                'required' => false,
                'disabled' => true,
                'block_prefix' => 'location_name',
                'attr' => [
                    'placeholder' => 'location.placeholder'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'location.city',
                'required' => false,
                'disabled' => true,
                'block_prefix' => 'location_half',
                'attr' => [
                    'placeholder' => 'location.placeholder'
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'location.country',
                'required' => false,
                'disabled' => true,
                'block_prefix' => 'location_half',
                'attr' => [
                    'placeholder' => 'location.placeholder'
                ]
            ])
            ->add('lat', TextType::class, [
                'label' => 'location.lat',
                'block_prefix' => 'location_inner',
            ])
            ->add('long', TextType::class, [
                'label' => 'location.long',
                'block_prefix' => 'location_inner',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'block_prefix' => null
        ]);
    }
}
