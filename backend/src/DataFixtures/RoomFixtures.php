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

        for ($i = 0; $i < 450; $i++) {
            $room = new Room();
            $room->setName('Chambre ' . strtoupper($faker->bothify('??-###')));
            $room->setDescription($faker->paragraph(2));
            $room->setPricePerNight($faker->randomFloat(2, 50, 300));
            $room->setCapacity($faker->numberBetween(1, 5));

            $hotel = $manager->getRepository(Hotel::class)->find($faker->numberBetween(1, 50));
            if ($hotel) {
                $room->setHotel($hotel);
                $manager->persist($room);
            }
        }

        $manager->flush();
    }
}
