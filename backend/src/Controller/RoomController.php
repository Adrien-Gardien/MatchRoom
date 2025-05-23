<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Entity\Room;
use App\Repository\RefreshTokenRepository;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/room', name: 'app_room_')]
final class RoomController extends AbstractController
{
    
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(RoomRepository $repository): JsonResponse
    {
        $rooms = $repository->findAll();
    
        // Utiliser les groupes de sérialisation au lieu de mapper manuellement
        return $this->json($rooms, 200, [], [
            'groups' => ['room:read']
        ]);
    }

    // Get first 20 rooms
    #[Route('/first-20', name: 'first-20', methods: ['GET'])]
    public function first20(RoomRepository $repository): JsonResponse
    {
        $rooms = $repository->findBy([], null, 20);
        
        // Utiliser les groupes de sérialisation
        return $this->json($rooms, 200, [], [
            'groups' => ['room:read']
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Room $room): JsonResponse
    {
        // Utiliser les groupes de sérialisation
        return $this->json($room, 200, [], [
            'groups' => ['room:read']
        ]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
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
