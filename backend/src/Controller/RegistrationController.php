<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class RegistrationController extends AbstractController
{
    private $emailService;
    
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        
        $em->persist($user);
        $em->flush();

        $verificationCode = rand(100000, 999999);
        $value = json_encode([
            'verification_code' => $verificationCode,
            'user_email' => $user->getEmail(),
        ]);
        $cookie = new Cookie(
            'informations',
            $value,
            time() + 900,
            '/',
            null,
            false,
            true
        );
        $response = new JsonResponse(['message' => 'User created'], Response::HTTP_CREATED);
        $response->headers->setCookie($cookie);
    
        $this->emailService->sendConfirmationEmail($user->getEmail(), $verificationCode);

        return $response;
    }
}
