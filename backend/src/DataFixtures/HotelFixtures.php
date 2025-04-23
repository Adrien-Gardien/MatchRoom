<?php

namespace App\DataFixtures;

use App\Entity\Hotel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HotelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $hotel = new Hotel();
            $hotel->setName($faker->company . ' Hôtel');
            $hotel->setAddress($faker->streetAddress);
            $hotel->setCity($faker->city);
            $hotel->setCountry($faker->country);
            $hotel->setDescription($faker->paragraph(3));
            $hotel->setImage('https://picsum.photos/640/480?random=' . $i);

            $manager->persist($hotel);
        }

        $manager->flush();
    }
}
