<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partie', HiddenType::class, [
                'mapped' => false
            ])
            ->add('team_blue', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
                'label' => 'Rejoindre l\'équipe bleue'
            ])
            ->add('team_red', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-danger'
                ],
                'label' => 'Rejoindre l\'équipe rouge'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
