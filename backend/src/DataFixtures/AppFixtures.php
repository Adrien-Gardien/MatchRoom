<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $hotelFixtures = new HotelFixtures();
        $roomFixtures = new RoomFixtures();
        $userFixtures = new UserFixtures($this->hasher);
        $ambianceFixtures = new AmbianceFixtures();
        $badgeFixtures = new BadgeFixtures();
        $ratingFixtures = new RatingFixtures();
        $serviceFixtures = new ServiceFixtures();

        $hotelFixtures->load($manager);
        $roomFixtures->load($manager);
        $userFixtures->load($manager);
        $ambianceFixtures->load($manager);
        $badgeFixtures->load($manager);
        $ratingFixtures->load($manager);
        $serviceFixtures->load($manager);
    }
}
