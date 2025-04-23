<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $hotelFixtures = new HotelFixtures();
        $roomFixtures = new RoomFixtures();
        $hotelFixtures->load($manager);
        $roomFixtures->load($manager);

    }
}
