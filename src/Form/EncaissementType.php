<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Encaissement;
use App\Entity\Vente;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncaissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$this->vendeur = $options['vendeur'];
		$vendeur = $this->vendeur;
        $builder
            //->add('quantite')
            ->add('montant', IntegerType::class,[
	            'attr'=>['class'=>"form-control", 'placeholder'=>"Montant payé", 'autocomplete'=>"off"],
	            'label'=>"Montant versé"
            ])
            //->add('rap')
            //->add('qteRestant')
            //->add('createdAt')
            ->add('vente', EntityType::class,[
	            'attr'=>['class' => 'form-control select2'],
	            'class' => Vente::class,
	            'query_builder' => function(EntityRepository $er) use($vendeur){
		            return $er->getListByVendeur($vendeur);
	            },
	            //'choice_label' => 'titre',
	            'label' => 'Album',
            ])
            //->add('recouvrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Encaissement::class,
	        'vendeur' => null
        ]);
    }
}
