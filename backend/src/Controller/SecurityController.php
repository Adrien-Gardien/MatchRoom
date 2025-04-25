<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RefreshTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
final class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request, RefreshTokenManager $refreshTokenManager): JsonResponse
    {
        $token = $request->cookies->get('REFRESH_TOKEN');

        if ($token) {
            $storedToken = $refreshTokenManager->get($token);
            if ($storedToken) {
                $refreshTokenManager->invalidate($storedToken);
            }
        }

        $cookie = Cookie::create('REFRESH_TOKEN')
            ->withValue('')
            ->withExpires(new \DateTime('-1 hour'))
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('strict');

        return new JsonResponse(['message' => 'Logged out'], 200, [
            'Set-Cookie' => (string) $cookie,
        ]);
    }

    #[Route('/token/refresh', name: 'app_refresh_token')]
    public function refresh(
        Request $request,
        RefreshTokenManager $refreshTokenManager,
        JWTTokenManagerInterface $jwtTokenManager,
    ): JsonResponse {
        $refreshToken = $request->cookies->get('REFRESH_TOKEN');

        if (!$refreshToken) {
            return new JsonResponse(['error' => 'Refresh token missing'], 401);
        }

        $storedToken = $refreshTokenManager->get($refreshToken);

        if (!$storedToken || $storedToken->getExpiresAt() < new \DateTime()) {
            return new JsonResponse(['error' => 'Refresh token expired'], 401);
        }

        $user = $storedToken->getUser();
        $newJwt = $jwtTokenManager->create($user);
        $newRefreshToken = $refreshTokenManager->rotate($storedToken);

        $cookie = Cookie::create('REFRESH_TOKEN')
            ->withValue($newRefreshToken->getToken())
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('strict')
            ->withExpires($newRefreshToken->getExpiresAt());

        $cookieJwt = Cookie::create('BEARER')
            ->withValue($newJwt)
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('strict')
            ->withExpires(new \DateTime('+900 seconds'));

        return new JsonResponse(['token' => $newJwt], 200, ['Set-Cookie' => (string) $cookie + $cookieJwt]);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }

        $constraints = new Assert\Collection([
            'fullName' => [
                new Assert\NotBlank(['message' => 'Le nom complet est obligatoire']),
                new Assert\Length(['min' => 2, 'max' => 255]),
            ],
            'email' => [
                new Assert\NotBlank(['message' => 'L\'email est obligatoire']),
                new Assert\Email(['message' => 'L\'email {{ value }} n\'est pas valide']),
            ],
            'password' => [
                new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire']),
                new Assert\Length([
                    'min' => 8,
                    'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                ]),
            ],
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $errors[str_replace(['[', ']'], '', $propertyPath)] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['fullName']);
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Inscription réussie',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
            ]
        ], 201);
    }
}
