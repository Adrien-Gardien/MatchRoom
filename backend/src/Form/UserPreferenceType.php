<?php

namespace App\Form;

use App\Entity\Ambiance;
use App\Entity\Service;
use App\Entity\User;
use App\Entity\UserPreference;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('budget')
            ->add('location')
            ->add('userId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('ambiance', EntityType::class, [
                'class' => Ambiance::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserPreference::class,
        ]);
    }
}
