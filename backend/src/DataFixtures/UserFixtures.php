<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Hotel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $hotels = $manager->getRepository(Hotel::class)->findAll();

        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $verificationCode = $faker->unique()->numerify('######'); // Code à six chiffres
            $user->setEmail($faker->unique()->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(['ROLE_ADMIN'])
                ->setIsVerified(true)
                ->setPassword($this->hasher->hashPassword($user, 'adminpass'))
                ->setVerificationCode($verificationCode);

            $manager->persist($user);
        }

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $verificationCode = $faker->unique()->numerify('######'); // Code à six chiffres
            $user->setEmail($faker->unique()->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(['ROLE_USER'])
                ->setIsVerified($faker->boolean(80))
                ->setPassword($this->hasher->hashPassword($user, 'userpass'))
                ->setVerificationCode($verificationCode);

            $manager->persist($user);
        }

        if (!empty($hotels)) {
            for ($i = 0; $i < 20; $i++) {
                $user = new User();
                $verificationCode = $faker->unique()->numerify('######'); // Code à six chiffres
                $user->setEmail($faker->unique()->email())
                    ->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName())
                    ->setRoles(['ROLE_HOTELIER'])
                    ->setHotel($faker->randomElement($hotels))
                    ->setIsVerified($faker->boolean(90))
                    ->setPassword($this->hasher->hashPassword($user, 'hotelpass'))
                    ->setVerificationCode($verificationCode);

                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}
