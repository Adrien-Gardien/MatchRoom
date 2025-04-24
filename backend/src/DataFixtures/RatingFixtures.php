<?php

namespace App\DataFixtures;

use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RatingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();
        $rooms = $manager->getRepository(Room::class)->findAll();

            for ($i = 0; $i < 100; $i++) {  // Par exemple, générer 100 évaluations
                $rating = new Rating();
                $rating->setRating($faker->numberBetween(1, 5))  // Note entre 1 et 5
                ->setComment($faker->text(200))  // Commentaire aléatoire de 200 caractères
                ->setDate($faker->dateTimeThisYear())  // Date de l'évaluation cette année
                ->setAuthorId($faker->randomElement($users))  // Utilisateur random parmi les utilisateurs
                ->setRoomId($faker->randomElement($rooms));  // Chambre random parmi les chambres

                $manager->persist($rating);
            }


        $manager->flush();
    }
}
