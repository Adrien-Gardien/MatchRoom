<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\Hotel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Liste des services possibles pour une chambre
        $possibleServices = [
            'Wi-Fi',
            'Climatisation',
            'Mini-bar',
            'Télévision',
            'Coffre-fort',
            'Service en chambre',
            'Room service 24h/24',
            'Nettoyage quotidien',
            'Petit-déjeuner inclus',
            'Salle de bain privative',
            'Produits de toilette',
            'Machine à café/thé',
            'Bureau',
            'Fer à repasser',
            'Sèche-cheveux',
            'Chambre non-fumeur',
            'Vue sur mer',
            'Vue sur la ville',
            'Balcon',
            'Piscine privée'
        ];

        // Récupérer tous les hôtels existants
        $hotels = $manager->getRepository(Hotel::class)->findAll();

        // S'assurer qu'il y a des hôtels avant de continuer
            for ($i = 0; $i < 450; $i++) {
                $room = new Room();
                $room->setName('Chambre ' . strtoupper($faker->bothify('??-###')));
                $room->setDescription($faker->paragraph(2));
                $room->setPricePerNight($faker->randomFloat(2, 50, 300));
                $room->setCapacity($faker->numberBetween(1, 5));
                $randomImageNumber = $faker->numberBetween(1, 18);
                $room->setImage("room-$randomImageNumber.jpg");

                // Générer un tableau aléatoire de services
                $services = $faker->randomElements($possibleServices, $faker->numberBetween(1, 6));
                $room->setService($services);

                // Sélectionner un hôtel au hasard parmi les hôtels existants
                $hotel = $faker->randomElement($hotels);
                $room->setHotel($hotel);

                $manager->persist($room);
            }

            $manager->flush();
        }
}