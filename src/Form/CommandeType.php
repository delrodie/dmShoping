<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
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
            ->add('idTransaction')
            ->add('statusTransaction')
            ->add('telTransaction')
            ->add('createdAt')
            ->add('dateTransaction')
            ->add('timeTransaction')
            ->add('album')
            ->add('localite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
