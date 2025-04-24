<?php

namespace App\DataFixtures;

use App\Entity\Service;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $rooms = $manager->getRepository(Room::class)->findAll();

            $serviceNames = [
                'Wi-Fi gratuit',
                'Service de chambre',
                'Petit-déjeuner',
                'Spa et bien-être',
                'Piscine',
                'Salle de sport',
                'Navette aéroport',
                'Parking',
                'Concierge',
                'Blanchisserie'
            ];

            foreach ($serviceNames as $name) {
                $service = new Service();
                $service->setName($name);
                $service->setDescription($faker->sentence(10));

                $randomRooms = $faker->randomElements($rooms, $faker->numberBetween(1, 5));
                foreach ($randomRooms as $room) {
                    $service->addRoom($room);
                }

                $manager->persist($service);
            }

            $manager->flush();
        }
}
