<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Vendeur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('reference')
            //->add('montant')
            ->add('tva', CheckboxType::class,[
                'attr'=>['class' => 'form-check-input'],
                'label' => 'Appliquer la TVA (18%)',
                'required'=>false
            ])
            //->add('ttc')
            //->add('flag')
            //->add('createdAt')
            //->add('updatedAt')
            ->add('date', TextType::class,[
                'attr'=>['class'=>'form-control datepicker-here', 'data-language' => 'fr', 'data-date-format'=>"yyyy-mm-dd", 'data-auto-close'=>true, 'autocomplete'=>"off"],
                'label'=> "Date de facturaction"
            ])
            ->add('vendeur', EntityType::class,[
                'attr'=>['class' => 'form-control select2'],
                'class' => Vendeur::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'nom',
                'label' => 'Vendeur'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
