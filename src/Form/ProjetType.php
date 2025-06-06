<?php

namespace App\Form;

use App\Entity\Developpeur;
use App\Entity\Hebergement;
use App\Entity\Projet;
use App\Entity\Technologie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('type')
            ->add('description')
            ->add('accesCodeSource')
            ->add('url')
            ->add('accesEnvironnement')
            ->add('created', null, [
                'widget' => 'single_text',
            ])
            ->add('updated', null, [
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'À faire' => 'à faire',
                    'En cours' => 'en cours',
                    'Terminé' => 'terminé',
                ],
                'label' => 'Statut du projet',
                'placeholder' => 'Choisissez un statut',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('technologie', EntityType::class, [
                'class' => Technologie::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('developpeur', EntityType::class, [
                'class' => Developpeur::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            
            ->add('hebergement', EntityType::class, [
                'class' => Hebergement::class,
                'choice_label' => 'fournisseur',
                'multiple' => true,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-info']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
