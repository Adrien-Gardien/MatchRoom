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

        // Récupérer tous les hôtels existants
        $hotels = $manager->getRepository(Hotel::class)->findAll();

        // S'assurer qu'il y a des hôtels avant de continuer
        if (!empty($hotels)) {
            for ($i = 0; $i < 450; $i++) {
                $room = new Room();
                $room->setName('Chambre ' . strtoupper($faker->bothify('??-###')));
                $room->setDescription($faker->paragraph(2));
                $room->setPricePerNight($faker->randomFloat(2, 50, 300));
                $room->setCapacity($faker->numberBetween(1, 5));
                $room->setImage('https://picsum.photos/640/480?random=' . $i);


                // Sélectionner un hôtel au hasard parmi les hôtels existants
                $hotel = $faker->randomElement($hotels);
                $room->setHotel($hotel);

                $manager->persist($room);
            }

            $manager->flush();
        }
    }
}
