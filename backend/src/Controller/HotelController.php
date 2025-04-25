<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Repository\HotelRepository;
use App\Repository\ImageRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api')]
final class HotelController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private NormalizerInterface $normalizer
    ) {}

    #[Route('/hotels', name: 'app_hotel_list', methods: ['GET'])]
    public function index(Request $request, HotelRepository $hotelRepository, ImageRepository $imageRepository): JsonResponse
    {
        $amount = $request->query->get('amount');

        // Récupérer les hôtels
        if ($amount) {
            $hotels = $hotelRepository->findFirstAmount($amount);
        } else {
            $hotels = $hotelRepository->findAll();
        }

        // Extraire tous les IDs d'hôtels
        $hotelIds = array_map(function ($hotel) {
            return $hotel->getId();
        }, $hotels);

        // Récupérer toutes les images pour ces hôtels en une seule requête
        $imagesByHotelId = $imageRepository->findByMultipleHotelIds($hotelIds);

        // Créer un tableau pour la réponse JSON
        $response = [];
        
        foreach ($hotels as $hotel) {
            $hotelId = $hotel->getId();
            
            // Construction du contexte de normalisation pour éviter les références circulaires
            $context = (new ObjectNormalizerContextBuilder())
                ->withCircularReferenceHandler(function ($object) {
                    return $object->getId();
                })
                ->withGroups(['hotel_list'])
                ->withSkipNullValues(true)
                ->toArray();
            
            // Sérialiser l'hôtel sans ses images
            $hotelArray = $this->normalizer->normalize($hotel, 'json', array_merge(
                $context, 
                [AbstractNormalizer::IGNORED_ATTRIBUTES => ['images']]
            ));
            
            // Ajouter les images manuellement
            $hotelArray['images'] = [];
            if (isset($imagesByHotelId[$hotelId])) {
                $hotelArray['images'] = $this->normalizer->normalize(
                    $imagesByHotelId[$hotelId], 
                    'json', 
                    [
                        'groups' => ['image_list'],
                        AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                            return $object->getId();
                        }
                    ]
                );
            }
            
            $response[] = $hotelArray;
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
    
    #[Route('/hotels/{id}/rooms', name: 'app_hotel_rooms', methods: ['GET'])]
    public function rooms(Hotel $hotel, RoomRepository $roomRepository, ImageRepository $imageRepository): JsonResponse
    {
        $rooms = $hotel->getRooms();
        
        // Extraire tous les IDs des chambres
        $roomIds = array_map(function ($room) {
            return $room->getId();
        }, $rooms->toArray());
        
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
            
            $response[] = $roomArray;
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/hotels/{id}', name: 'app_hotel_show', methods: ['GET'])]
    public function show(Hotel $hotel): JsonResponse
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withCircularReferenceHandler(function ($object) {
                return $object->getId();
            })
            ->withGroups(['hotel_details'])
            ->toArray();

        return $this->json($hotel, Response::HTTP_OK, [], $context);
    }

    #[Route('/hotels', name: 'app_hotel_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $hotel = $this->serializer->deserialize(
            $request->getContent(),
            Hotel::class,
            'json'
        );
        
        // L'utilisateur connecté devient le propriétaire de l'hôtel
        $hotel->setOwnerId($this->getUser());

        $this->entityManager->persist($hotel);
        $this->entityManager->flush();

        $context = (new ObjectNormalizerContextBuilder())
            ->withCircularReferenceHandler(function ($object) {
                return $object->getId();
            })
            ->withGroups(['hotel_details'])
            ->toArray();

        return $this->json($hotel, Response::HTTP_CREATED, [], $context);
    }

    #[Route('/hotels/{id}', name: 'app_hotel_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Hotel $hotel): JsonResponse
    {
        // Vérifier si l'utilisateur est le propriétaire de l'hôtel
        if ($hotel->getOwnerId() !== $this->getUser()) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $updatedHotel = $this->serializer->deserialize(
            $request->getContent(),
            Hotel::class, 
            'json', 
            ['object_to_populate' => $hotel]
        );
        
        $this->entityManager->flush();

        $context = (new ObjectNormalizerContextBuilder())
            ->withCircularReferenceHandler(function ($object) {
                return $object->getId();
            })
            ->withGroups(['hotel_details'])
            ->toArray();

        return $this->json($updatedHotel, Response::HTTP_OK, [], $context);
    }

    #[Route('/hotels/{id}', name: 'app_hotel_delete', methods: ['DELETE'])]
    public function delete(Hotel $hotel): JsonResponse
    {
        // Vérifier si l'utilisateur est le propriétaire de l'hôtel
        if ($hotel->getOwnerId() !== $this->getUser()) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($hotel);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
