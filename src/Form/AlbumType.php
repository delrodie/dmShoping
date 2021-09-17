<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Artiste;
use App\Entity\Genre;
use App\Entity\Sygesca\Region;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form-control', 'placeholder' => "Titre de l'album", 'autocomplete'=>"off"],
                'label'=>"Titre"
            ])
            ->add('prixVente', IntegerType::class,[
                'attr'=>['class'=>'form-control', 'placeholder'=>"Prix de vente standard", 'autocomplete' => 'off'],
                'label'=>"Prix de vente"
            ])
            //->add('nonSticke')
            //->add('sticke')
            //->add('distribue')
            ->add('stock', IntegerType::class,[
                'attr'=>['class'=>'form-control', 'placeholder'=>"Quantité de CD", 'autocomplete' => 'off'],
                'label'=>"Nombre de CD",
                'required'=>false
            ])
            ->add('description', TextareaType::class,[
                'attr'=>['class'=>'form-control']
            ])
            ->add('pochette', FileType::class,[
                'attr'=>['class'=>"dropify", 'data-preview' => ".preview"],
                'label' => "Télécharger la pochette",
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => "1000000k",
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => "Votre fichier doit être de type image"
                    ])
                ],
                'required' => false
            ])
            //->add('slug')
            ->add('artiste', EntityType::class,[
                'attr'=>['class' => 'form-control select2'],
                'class' => Artiste::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'nom',
                'label' => 'Artiste'
            ])
            ->add('genre', EntityType::class,[
                'attr'=>['class' => 'form-control select2'],
                'class' => Genre::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'libelle',
                'label' => 'Genre musical'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}
