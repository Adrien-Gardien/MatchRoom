<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
final class RoomController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RoomRepository $roomRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/rooms', name: 'app_room_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $rooms = $this->roomRepository->findAll();
        
        return $this->json([
            'data' => $rooms,
        ], Response::HTTP_OK, [], ['groups' => 'room:read']);
    }
    
    #[Route('/rooms/{id}', name: 'app_room_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);
        
        if (!$room) {
            return $this->json(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json([
            'data' => $room,
        ], Response::HTTP_OK, [], ['groups' => 'room:read']);
    }
    
    #[Route('/rooms', name: 'app_room_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $room = new Room();
        $room->setName($data['name'] ?? '');
        $room->setDescription($data['description'] ?? '');
        $room->setCapacity($data['capacity'] ?? 0);
        $room->setPrice($data['price'] ?? 0);
        $room->setAvailable($data['available'] ?? true);
        $room->setCreatedAt(new \DateTimeImmutable());
        $room->setUpdatedAt(new \DateTimeImmutable());
        
        $errors = $this->validator->validate($room);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(['message' => $errorsString], Response::HTTP_BAD_REQUEST);
        }
        
        $this->entityManager->persist($room);
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Room created successfully',
            'data' => $room,
        ], Response::HTTP_CREATED, [], ['groups' => 'room:read']);
    }
    
    #[Route('/rooms/{id}', name: 'app_room_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);
        
        if (!$room) {
            return $this->json(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $room->setName($data['name']);
        }
        
        if (isset($data['description'])) {
            $room->setDescription($data['description']);
        }
        
        if (isset($data['capacity'])) {
            $room->setCapacity($data['capacity']);
        }
        
        if (isset($data['price'])) {
            $room->setPrice($data['price']);
        }
        
        if (isset($data['available'])) {
            $room->setAvailable($data['available']);
        }
        
        $room->setUpdatedAt(new \DateTimeImmutable());
        
        $errors = $this->validator->validate($room);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(['message' => $errorsString], Response::HTTP_BAD_REQUEST);
        }
        
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Room updated successfully',
            'data' => $room,
        ], Response::HTTP_OK, [], ['groups' => 'room:read']);
    }
    
    #[Route('/rooms/{id}', name: 'app_room_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);
        
        if (!$room) {
            return $this->json(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }
        
        $this->entityManager->remove($room);
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Room deleted successfully'
        ], Response::HTTP_OK);
    }
}
