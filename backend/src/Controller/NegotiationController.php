<?php

namespace App\Controller;

use App\Entity\Negotiation;
use App\Enum\NegotiationStatusEnum;
use App\Repository\NegotiationRepository;
use App\Repository\RoomRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
final class NegotiationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NegotiationRepository $negotiationRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/negotiations', name: 'app_negotiation_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $negotiations = $this->negotiationRepository->findAll();
        
        return $this->json([
            'negotiations' => $negotiations,
        ], Response::HTTP_OK, [], ['groups' => 'negotiation:read']);
    }
    
    #[Route('/negotiations/{id}', name: 'app_negotiation_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $negotiation = $this->negotiationRepository->find($id);
        
        if (!$negotiation) {
            return $this->json(['error' => 'Negotiation not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json([
            'negotiation' => $negotiation,
        ], Response::HTTP_OK, [], ['groups' => 'negotiation:read']);
    }
    
    #[Route('/negotiations', name: 'app_negotiation_create', methods: ['POST'])]
    public function create(LoggerInterface $logger, Request $request, RoomRepository $roomRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $roomId = $data['roomId'];
        $proposedPrice = $data['proposedPrice'];

        $logger->info($data['roomId']);

        $room = $roomRepository->findOneBy(['id' => $roomId]);

        $negotiation = new Negotiation();
        
        $negotiation->setProposedPrice($proposedPrice);
        $negotiation->setRoomId($room);
        $negotiation->setStatus(NegotiationStatusEnum::PENDING);
        $negotiation->setCreatedAt(new DateTimeImmutable());
        $negotiation->setUpdatedAt(new DateTimeImmutable());
        
        $this->entityManager->persist($negotiation);
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Negotiation created successfully',
            'negotiation' => $negotiation,
        ], Response::HTTP_CREATED, [], ['groups' => 'negotiation:read']);
    }
    
    #[Route('/negotiations/{id}', name: 'app_negotiation_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $negotiation = $this->negotiationRepository->find($id);
        
        if (!$negotiation) {
            return $this->json(['error' => 'Negotiation not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        // Update negotiation properties based on $data
        
        $errors = $this->validator->validate($negotiation);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }
        
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Negotiation updated successfully',
            'negotiation' => $negotiation,
        ], Response::HTTP_OK, [], ['groups' => 'negotiation:read']);
    }
    
    #[Route('/negotiations/{id}', name: 'app_negotiation_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $negotiation = $this->negotiationRepository->find($id);
        
        if (!$negotiation) {
            return $this->json(['error' => 'Negotiation not found'], Response::HTTP_NOT_FOUND);
        }
        
        $this->entityManager->remove($negotiation);
        $this->entityManager->flush();
        
        return $this->json(['message' => 'Negotiation deleted successfully'], Response::HTTP_OK);
    }
}
