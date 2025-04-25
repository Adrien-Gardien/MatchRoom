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
            $randomImageNumber = $faker->numberBetween(1, 18);
            $hotel->setImage("hotel-$randomImageNumber.jpg");
            $hotel->setAmbiance($faker->randomElement([
                'Chic',
                'Moderne',
                'Chaleureuse',
                'Luxueuse',
                'Minimaliste',
                'Bohème',
                'Vintage',
                'Écologique',
                'Romantique',
                'Urbaine',
                'Zen',
                'Maritime',
                'Montagnarde',
                'Tropicale',
                'Historique',
                'Futuriste',
                'Industrielle',
                'Rustique',
                'Ethnique',
                'Glamour',
                'Sportive',
                'Gastronomique',
                'Culturelle',
                'Professionnelle',
                'Festive'
            ]));
            $manager->persist($hotel);
        }

        $manager->flush();
    }
}
