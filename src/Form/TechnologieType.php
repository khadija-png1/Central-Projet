<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Projet;
use App\Entity\Technologie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechnologieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('version')
            ->add('created', null, [
                'widget' => 'single_text',
            ])
            ->add('updated', null, [
                'widget' => 'single_text',
            ])

            
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-info']
            ])        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Technologie::class,
        ]);
    }
}
