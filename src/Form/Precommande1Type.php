<?php

namespace App\Form;

use App\Entity\Precommande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Precommande1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference')
            ->add('nom')
            ->add('tel')
            ->add('adresse')
            ->add('email')
            ->add('quantite')
            ->add('montant')
            ->add('flag')
            ->add('idTransaction')
            ->add('statusTransaction')
            ->add('createdAt')
            ->add('localite')
            ->add('album')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Precommande::class,
        ]);
    }
}
