<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Vente;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantite', IntegerType::class,[
                'attr'=>['class'=>"form-control", 'placeholder'=>"Nombre de CD commandés", 'autocomplete'=>"off"],
                'label'=>"Quantité"
            ])
            ->add('pu', IntegerType::class,[
                'attr'=>['class'=>"form-control", 'placeholder'=>"Prix unitaire", 'autocomplete'=>"off"],
                'label'=>"Prix Unitaire"
            ])
            //->add('montant')
            //->add('createdAt')
            //->add('facture')
            ->add('album', EntityType::class,[
                'attr'=>['class' => 'form-control select2'],
                'class' => Album::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                //'choice_label' => 'titre',
                'label' => 'Album',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}
