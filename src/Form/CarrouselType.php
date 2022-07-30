<?php

namespace App\Form;

use App\Entity\Carrousel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CarrouselType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', FileType::class, [
            'data_class' => null, 
            'required' => false,
            'label' =>"chargez une image",
        ])            
            ->add('titre')
            ->add('description')            
            ->add('alt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carrousel::class,
        ]);
    }
}
