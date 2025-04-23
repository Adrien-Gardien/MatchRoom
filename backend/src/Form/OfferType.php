<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Room;
use App\Entity\User;
use App\Enum\OfferType;
use App\Enum\OfferStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('proposedPrice')
            ->add('status', ChoiceType::class, [
                'choices' => OfferStatus::cases(),
                'choice_label' => fn(OfferStatus $choice) => match ($choice) {
                    OfferStatus::PENDING => 'En attente',
                    OfferStatus::ACCEPTED => 'Acceptée',
                    OfferStatus::REJECTED => 'Rejetée',
                },
                'choice_value' => fn(?OfferStatus $choice) => $choice?->value,
            ])
            ->add('offerType', ChoiceType::class, [
                'choices' => OfferType::cases(),
                'choice_label' => fn(OfferType $choice) => match ($choice) {
                    OfferType::OFFER => 'Offre',
                    OfferType::COUNTER_OFFER => 'Contre-offre',
                },
                'choice_value' => fn(?OfferType $choice) => $choice?->value,
            ])
            ->add('offerDate', null, [
                'widget' => 'single_text',
            ])
            ->add('userId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('roomId', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
