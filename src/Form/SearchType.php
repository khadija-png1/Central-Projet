<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder->add('search', TextType::class, [
            'required' => false,
            'attr' => ['placeholder' => 'Rechercher ...',
            'class' => 'form-control flex-grow-1',
            // Utilise flex-grow pour prendre l'espace disponible
                //'style' => 'width: 1000px;' // Limite à 80% de l'écran

        ],

        ]);


    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([]);
    }
}
