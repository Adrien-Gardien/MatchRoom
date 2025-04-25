<?php

namespace App\DataFixtures;

use App\Entity\Hotel;
use App\Entity\Image;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HotelFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de 10 utilisateurs propriétaires d'hôtels
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName($faker->name());
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_OWNER']);
            
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password'
            );
            $user->setPassword($hashedPassword);
            
            $manager->persist($user);
            $users[] = $user;
        }

        // Création de 20 hôtels
        for ($i = 0; $i < 20; $i++) {
            $hotel = new Hotel();
            
            $hotel->setName($faker->company() . ' Hotel');
            $hotel->setDescription($faker->paragraph(3));
            $hotel->setAddress($faker->streetAddress());
            $hotel->setCity($faker->city());
            $hotel->setZipCode((int) $faker->postcode());
            $hotel->setCountry($faker->country());
            
            // Attribuer un propriétaire aléatoire parmi les utilisateurs créés
            $randomOwner = $users[array_rand($users)];
            $hotel->setOwnerId($randomOwner);
            
            // Ajouter entre 3 et 8 images pour chaque hôtel
            $imageCount = rand(3, 8);
            for ($j = 0; $j < $imageCount; $j++) {
                $image = new Image();
                
                $imageUrl = "/image.png";
                
                $image->setUrl($imageUrl);
                $image->setHotel($hotel);
                
                $manager->persist($image);
            }
            
            $manager->persist($hotel);
            
            // Création de 3 à 10 chambres pour chaque hôtel
            $roomCount = rand(3, 10);
            for ($k = 0; $k < $roomCount; $k++) {
                $room = new Room();
                
                $room->setName($faker->word() . ' Room');
                $room->setDescription($faker->paragraph(2));
                $room->setCapacity(rand(1, 4));
                $room->setPrice(rand(50, 500));
                $room->setAvailable(true);
                $room->setCreatedAt(new \DateTimeImmutable());
                $room->setUpdatedAt(new \DateTimeImmutable());
                $room->setHotelId($hotel);
                
                $manager->persist($room);
                
                // Ajouter des images pour la chambre
                $roomImageCount = rand(2, 5);
                for ($l = 0; $l < $roomImageCount; $l++) {
                    $roomImage = new Image();
                    $roomImage->setUrl("/image.png");
                    $roomImage->setRoom($room);
                    
                    $manager->persist($roomImage);
                }
            }
        }

        $manager->flush();
    }
}
