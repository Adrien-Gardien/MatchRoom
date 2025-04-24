<?php

namespace App\DataFixtures;

use App\Entity\Badge;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BadgeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $badges = [
            [
                'name' => 'Explorateur débutant',
                'description' => 'A effectué 10 réservations sur la plateforme.',
                'image' => 'badges/explorateur-debutant.png'
            ],
            [
                'name' => 'Voyageur confirmé',
                'description' => 'A effectué 50 réservations sur la plateforme.',
                'image' => 'badges/voyageur-confirme.png'
            ],
            [
                'name' => 'Globe-trotteur',
                'description' => 'A effectué 100 réservations ou plus.',
                'image' => 'badges/globe-trotteur.png'
            ],
            [
                'name' => 'Membre actif',
                'description' => 'Se connecte régulièrement et interagit avec les services.',
                'image' => 'badges/membre-actif.png'
            ],
            [
                'name' => 'Ancien fidèle',
                'description' => 'Inscrit depuis plus de 2 ans.',
                'image' => 'badges/ancien-fidele.png'
            ],
            [
                'name' => 'Découvreur de pépites',
                'description' => 'Laisse souvent des avis et aide la communauté.',
                'image' => 'badges/decouvreur.png'
            ]
        ];

        foreach ($badges as $data) {
            $badge = new Badge();
            $badge->setNamename($data['name']);
            $badge->setDescription($data['description']);
            $badge->setImage($data['image']);

            $manager->persist($badge);
        }

        $manager->flush();
    }
}
