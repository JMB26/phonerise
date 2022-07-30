<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('img' , FileType::class,[
                'label' =>"chargez une image",
                'data_class' => null,
                'required' =>false
            ])
            ->add('prix_unit')
            ->add('quantity')
            ->add('ref')
            ->add('marque')
            ->add('category' ,EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name'
              
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
