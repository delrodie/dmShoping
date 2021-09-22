<?php

namespace App\Form;

use App\Entity\Vendeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VendeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,[
                'attr'=>['class'=>"form-control", 'placeholder'=> "Nom", 'autocomplete'=>"off"],
                'label' => "Raison sociale",
            ] )
            ->add('situation', TextareaType::class,[
                'attr'=>['class'=>'form-control'],
                'label'=>"Situation géographique"
            ])
            ->add('contact',TextType::class,[
                'attr'=>['class'=>"form-control", 'placeholder'=> "Contact téléphonique", 'autocomplete'=>"off"],
                'label' => "Contact",
            ])
            //->add('credit')
            //->add('payer')
            //->add('reste')
            //->add('code')
            //->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vendeur::class,
        ]);
    }
}
