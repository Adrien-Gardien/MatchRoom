<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Repository\RefreshTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/refresh-token', name: 'app_refresh_token_')]
final class RefreshTokenController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(RefreshTokenRepository $repository): JsonResponse
    {
        $tokens = $repository->findAll();

        $data = array_map(function (RefreshToken $token) {
            return [
                'id' => $token->getId(),
                'token' => $token->getToken(),
                'expiresAt' => $token->getExpiresAt()?->format('Y-m-d H:i:s'),
                'userId' => $token->getUser()?->getId(),
            ];
        }, $tokens);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(RefreshToken $token): JsonResponse
    {
        return $this->json([
            'id' => $token->getId(),
            'token' => $token->getToken(),
            'expiresAt' => $token->getExpiresAt()?->format('Y-m-d H:i:s'),
            'userId' => $token->getUser()?->getId(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $token = new RefreshToken();
        $token->setToken($data['token'] ?? '')
              ->setExpiresAt(new \DateTime($data['expiresAt'] ?? '+1 day'));

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $token->setUser($user);
        }

        $em->persist($token);
        $em->flush();

        return $this->json([
            'message' => 'Jeton de rafraîchissement créé avec succès',
            'id' => $token->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        RefreshToken $token,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['token'])) {
            $token->setToken($data['token']);
        }

        if (isset($data['expiresAt'])) {
            $token->setExpiresAt(new \DateTime($data['expiresAt']));
        }

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $token->setUser($user);
        }

        $em->flush();

        return $this->json(['message' => 'Jeton de rafraîchissement mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(RefreshToken $token, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($token);
        $em->flush();

        return $this->json(['message' => 'Jeton de rafraîchissement supprimé avec succès']);
    }
}
