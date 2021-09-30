<?php

namespace App\Form;

use App\Entity\Precommande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrecommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('reference', TextType::class,['attr'=>['class'=>'form-control form-control-sm', 'autocomplete'=>"off"]])
            ->add('nom', TextType::class,['attr'=>['class'=>'form-control form-control-sm', 'autocomplete'=>"off"]])
            ->add('tel', TelType::class,['attr'=>['class'=>'form-control form-control-sm', 'autocomplete'=>"off"]])
            ->add('adresse', TextType::class,['attr'=>['class'=>'form-control form-control-sm', 'autocomplete'=>"off"]])
            ->add('email', EmailType::class,['attr'=>['class'=>'form-control form-control-sm', 'autocomplete'=>"off"]])
            //->add('quantite')
            //->add('montant')
            //->add('flag')
            //->add('idTransaction')
            //->add('statusTransaction')
            //->add('createdAt')
            //->add('localite')
            //->add('album')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Precommande::class,
        ]);
    }
}
