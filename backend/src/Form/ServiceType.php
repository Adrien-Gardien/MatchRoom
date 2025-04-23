<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Service;
use App\Entity\UserPreference;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('rooms', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('userPreferences', EntityType::class, [
                'class' => UserPreference::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
