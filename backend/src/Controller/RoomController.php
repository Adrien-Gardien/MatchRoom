<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api')]
final class RoomController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RoomRepository $roomRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly NormalizerInterface $normalizer
    ) {
    }

    #[Route('/rooms', name: 'app_room_list', methods: ['GET'])]
    public function index(Request $request, ImageRepository $imageRepository): JsonResponse
    {
        $amount = $request->query->get('amount');
        
        // Récupérer les chambres
        if ($amount) {
            $rooms = $this->roomRepository->findFirstAmount((int) $amount);
        } else {
            $rooms = $this->roomRepository->findAll();
        }
        
        // Extraire tous les IDs des chambres
        $roomIds = array_map(function ($room) {
            return $room->getId();
        }, $rooms);
        
        // Récupérer toutes les images pour ces chambres en une seule requête
        $imagesByRoomId = $imageRepository->findByMultipleRoomIds($roomIds);
        
        // Créer un tableau pour la réponse JSON
        $response = [];
        
        foreach ($rooms as $room) {
            $roomId = $room->getId();
            
            // Construction du contexte de normalisation pour éviter les références circulaires
            $context = (new ObjectNormalizerContextBuilder())
                ->withCircularReferenceHandler(function ($object) {
                    return $object->getId();
                })
                ->withGroups(['room_details'])
                ->withSkipNullValues(true)
                ->toArray();
                
            // Sérialiser la chambre sans ses images
            $roomArray = $this->normalizer->normalize($room, 'json', array_merge(
                $context, 
                [AbstractNormalizer::IGNORED_ATTRIBUTES => ['images']]
            ));
            
            // Ajouter les images manuellement
            $roomArray['images'] = [];
            if (isset($imagesByRoomId[$roomId])) {
                $roomArray['images'] = $this->normalizer->normalize(
                    $imagesByRoomId[$roomId], 
                    'json', 
                    [
                        'groups' => ['image_list'],
                        AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                            return $object->getId();
                        }
                    ]
                );
            }
            
            // Ajouter l'hôtel lié à la chambre
            $roomArray['hotel'] = $this->normalizer->normalize(
                $room->getHotelId(),
                'json',
                [
                    'groups' => ['hotel_list'],
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                        return $object->getId();
                    }
                ]
            );
            
            $response[] = $roomArray;
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/rooms/random', name: 'app_room_random', methods: ['GET'])]
    public function random(Request $request, ImageRepository $imageRepository): JsonResponse
    {
        $amount = $request->query->get('amount', 10); // Par défaut, 10 chambres aléatoires
        
        // Récupérer les chambres aléatoires
        $rooms = $this->roomRepository->findRandomAvailable((int) $amount);
        
        // Extraire tous les IDs des chambres
        $roomIds = array_map(function ($room) {
            return $room->getId();
        }, $rooms);
        
        // Récupérer toutes les images pour ces chambres en une seule requête
        $imagesByRoomId = $imageRepository->findByMultipleRoomIds($roomIds);
        
        // Créer un tableau pour la réponse JSON
        $response = [];
        
        foreach ($rooms as $room) {
            $roomId = $room->getId();
            
            // Construction du contexte de normalisation pour éviter les références circulaires
            $context = (new ObjectNormalizerContextBuilder())
                ->withCircularReferenceHandler(function ($object) {
                    return $object->getId();
                })
                ->withGroups(['room_details'])
                ->withSkipNullValues(true)
                ->toArray();
                
            // Sérialiser la chambre sans ses images
            $roomArray = $this->normalizer->normalize($room, 'json', array_merge(
                $context, 
                [AbstractNormalizer::IGNORED_ATTRIBUTES => ['images']]
            ));
            
            // Ajouter les images manuellement
            $roomArray['images'] = [];
            if (isset($imagesByRoomId[$roomId])) {
                $roomArray['images'] = $this->normalizer->normalize(
                    $imagesByRoomId[$roomId], 
                    'json', 
                    [
                        'groups' => ['image_list'],
                        AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                            return $object->getId();
                        }
                    ]
                );
            }
            
            // Ajouter l'hôtel lié à la chambre
            $roomArray['hotel'] = $this->normalizer->normalize(
                $room->getHotelId(),
                'json',
                [
                    'groups' => ['hotel_list'],
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                        return $object->getId();
                    }
                ]
            );
            
            $response[] = $roomArray;
        }

        return new JsonResponse($response, Response::HTTP_OK);
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
