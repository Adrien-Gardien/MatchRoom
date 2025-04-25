<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use App\Repository\HotelRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api')]
final class SearchController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RoomRepository $roomRepository,
        private readonly HotelRepository $hotelRepository,
        private readonly ImageRepository $imageRepository,
        private readonly SerializerInterface $serializer,
        private readonly NormalizerInterface $normalizer
    ) {
    }

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('query', '');
        
        if (empty($query)) {
            return $this->json(['message' => 'Search query is required'], Response::HTTP_BAD_REQUEST);
        }
        
        // Recherche des chambres dont le nom ou la description contient la requête
        // ou des chambres dont l'hôtel a le nom ou la ville correspondant à la requête
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('r')
            ->from('App\Entity\Room', 'r')
            ->leftJoin('r.hotelId', 'h')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('r.name', ':query'),
                $queryBuilder->expr()->like('r.description', ':query'),
                $queryBuilder->expr()->like('h.name', ':query'),
                $queryBuilder->expr()->like('h.city', ':query')
            ))
            ->andWhere('r.available = true')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('r.createdAt', 'DESC');
        
        $rooms = $queryBuilder->getQuery()->getResult();
        
        // Mise à jour du compte de résultats pour le frontend
        $numberOfResults = count($rooms);
        
        // Récupérer tous les IDs d'hôtels associés aux chambres
        $hotelIds = [];
        $roomIds = [];
        foreach ($rooms as $room) {
            $hotelId = $room->getHotelId()->getId();
            $roomIds[] = $room->getId();
            if (!in_array($hotelId, $hotelIds)) {
                $hotelIds[] = $hotelId;
            }
        }
        
        // Charger tous les hôtels concernés en une seule requête
        $hotels = $this->hotelRepository->findBy(['id' => $hotelIds]);
        $hotelsById = [];
        foreach ($hotels as $hotel) {
            $hotelsById[$hotel->getId()] = $hotel;
        }
        
        // Charger toutes les images des chambres en une seule requête
        $imagesByRoomId = $this->imageRepository->findByMultipleRoomIds($roomIds);
        
        // Créer un contexte pour la normalisation
        $roomContext = (new ObjectNormalizerContextBuilder())
            ->withCircularReferenceHandler(function ($object) {
                return $object->getId();
            })
            ->withGroups(['room_details'])
            ->withSkipNullValues(true)
            ->toArray();
            
        $hotelContext = (new ObjectNormalizerContextBuilder())
            ->withCircularReferenceHandler(function ($object) {
                return $object->getId();
            })
            ->withGroups(['hotel_details'])
            ->withSkipNullValues(true)
            ->toArray();
            
        $imageContext = (new ObjectNormalizerContextBuilder())
            ->withCircularReferenceHandler(function ($object) {
                return $object->getId();
            })
            ->withGroups(['image_list'])
            ->withSkipNullValues(true)
            ->toArray();
        
        // Construction du tableau de réponse avec les chambres, leurs hôtels et images associés
        $response = [];
        
        foreach ($rooms as $room) {
            $roomId = $room->getId();
            $roomArray = $this->normalizer->normalize($room, 'json', array_merge(
                $roomContext,
                [AbstractNormalizer::IGNORED_ATTRIBUTES => ['hotelId', 'images']]
            ));
            
            // Récupérer l'hôtel associé et l'ajouter à la chambre
            $hotelId = $room->getHotelId()->getId();
            if (isset($hotelsById[$hotelId])) {
                $hotel = $hotelsById[$hotelId];
                $roomArray['hotel'] = $this->normalizer->normalize(
                    $hotel,
                    'json',
                    $hotelContext
                );
            }
            
            // Ajouter les images de la chambre
            $roomArray['images'] = [];
            if (isset($imagesByRoomId[$roomId])) {
                $roomArray['images'] = $this->normalizer->normalize(
                    $imagesByRoomId[$roomId],
                    'json',
                    $imageContext
                );
            }
            
            $response[] = $roomArray;
        }
        
        return new JsonResponse([
            'data' => $response,
            'total' => $numberOfResults
        ], Response::HTTP_OK);
    }
}