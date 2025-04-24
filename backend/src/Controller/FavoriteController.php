<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use App\Repository\UserRepository;
use App\Repository\HotelRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/favorite', name: 'app_favorite_')]
final class FavoriteController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(FavoriteRepository $repository): JsonResponse
    {
        $favorites = $repository->findAll();

        $data = array_map(function (Favorite $favorite) {
            return [
                'id' => $favorite->getId(),
                'addedDate' => $favorite->getAddedDate()->format('Y-m-d H:i:s'),
                'userId' => $favorite->getUserId()?->getId(),
                'hotelId' => $favorite->getHotelId()?->getId(),
                'roomId' => $favorite->getRoomId()?->getId(),
            ];
        }, $favorites);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Favorite $favorite): JsonResponse
    {
        return $this->json([
            'id' => $favorite->getId(),
            'addedDate' => $favorite->getAddedDate()->format('Y-m-d H:i:s'),
            'userId' => $favorite->getUserId()?->getId(),
            'hotelId' => $favorite->getHotelId()?->getId(),
            'roomId' => $favorite->getRoomId()?->getId(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepo,
        HotelRepository $hotelRepo,
        RoomRepository $roomRepo,
        FavoriteRepository $favoriteRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
    
        $user = isset($data['userId']) ? $userRepo->find($data['userId']) : null;
        $hotel = isset($data['hotelId']) ? $hotelRepo->find($data['hotelId']) : null;
        $room = isset($data['roomId']) ? $roomRepo->find($data['roomId']) : null;
    
        $existingFavorite = $favoriteRepo->findOneBy([
            'userId' => $user,
            'hotelId' => $hotel,
            'roomId' => $room,
        ]);
    
        if ($existingFavorite) {
            return $this->json([
                'message' => 'Ce favori existe déjà.',
            ], Response::HTTP_CONFLICT);
        }
    
        $favorite = new Favorite();
        $favorite->setAddedDate(new \DateTime($data['addedDate'] ?? 'now'));
        $favorite->setUserId($user);
        $favorite->setHotelId($hotel);
        $favorite->setRoomId($room);
    
        $em->persist($favorite);
        $em->flush();
    
        return $this->json([
            'message' => 'Favori ajouté avec succès',
            'id' => $favorite->getId(),
        ], Response::HTTP_CREATED);
    }
    
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Favorite $favorite,
        EntityManagerInterface $em,
        UserRepository $userRepo,
        HotelRepository $hotelRepo,
        RoomRepository $roomRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['addedDate'])) {
            $favorite->setAddedDate(new \DateTime($data['addedDate']));
        }

        if (isset($data['userId'])) {
            $user = $userRepo->find($data['userId']);
            $favorite->setUserId($user);
        }

        if (isset($data['hotelId'])) {
            $hotel = $hotelRepo->find($data['hotelId']);
            $favorite->setHotelId($hotel);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepo->find($data['roomId']);
            $favorite->setRoomId($room);
        }

        $em->flush();

        return $this->json(['message' => 'Favori mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Favorite $favorite, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($favorite);
        $em->flush();

        return $this->json(['message' => 'Favori supprimé avec succès']);
    }
}
