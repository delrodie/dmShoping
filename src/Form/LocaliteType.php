<?php

namespace App\Form;

use App\Entity\Localite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lieu', TextType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>'off'],
                'label' => "Nom du lieu"
            ])
            ->add('prix', IntegerType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>'off'],
                'label' => "Prix de la livraison"
            ])
            ->add('statut', CheckboxType::class,[
                'attr'=>['class' => 'form-check-input'],
                'label' => 'Statut',
                'required'=>false
            ])
            //->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Localite::class,
        ]);
    }
}
