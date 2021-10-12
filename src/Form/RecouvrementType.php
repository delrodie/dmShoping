<?php

namespace App\Form;

use App\Entity\Recouvrement;
use App\Entity\Vendeur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecouvrementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroFactureDistributeur', TextType::class,[
                'attr'=>['class'=>'form-control', 'placeholder' => 'Reference facture du vendeur', 'autocomplete'=>"off"],
                'required'=>false,
                'label'=> "Facture vendeur"
            ])
            ->add('date', TextType::class,[
                'attr'=>['class'=>'form-control datepicker-here', 'data-language' => 'fr', 'data-date-format'=>"yyyy-mm-dd", 'data-auto-close'=>true, 'autocomplete'=>"off"],
                'label'=> "Date"
            ])
            ->add('montant', IntegerType::class,[
                'attr'=>['class'=>'form-control', 'placeholder' => 'Montant versé', 'autocomplete'=>"off"],
                'label'=> "Montant versé"
            ])
            //->add('flag')
            //->add('createdAt')
            //->add('updatedAt')
            ->add('vendeur', EntityType::class,[
                'attr'=>['class' => 'form-control select2'],
                'class' => Vendeur::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->listeDoit();
                },
                'choice_label' => 'vendeur',
                'label' => 'Vendeur'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recouvrement::class,
        ]);
    }
}
