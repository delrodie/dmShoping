<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Artiste;
use App\Entity\Pressage;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PressageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantite', IntegerType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label'=> "Quantité de CD"
            ])
            ->add('date', TextType::class,[
                'attr'=>['class'=>'form-control datepicker-here', 'data-language' => 'fr', 'data-date-format'=>"yyyy-mm-dd", 'data-auto-close'=>true, 'autocomplete'=>"off"],
                'label'=> "Date d'approvisionnement"
            ])
            ->add('album', EntityType::class,[
                'attr'=>['class' => 'form-control select2'],
                'class' => Album::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'titre',
                'label' => 'Album'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pressage::class,
        ]);
    }
}
