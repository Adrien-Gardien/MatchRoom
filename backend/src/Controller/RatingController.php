<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/rating', name: 'app_rating_')]
final class RatingController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(RatingRepository $repository): JsonResponse
    {
        $ratings = $repository->findAll();

        $data = array_map(function (Rating $rating) {
            return [
                'id' => $rating->getId(),
                'rating' => $rating->getRating(),
                'comment' => $rating->getComment(),
                'date' => $rating->getDate()?->format('Y-m-d'),
                'authorId' => $rating->getAuthorId()?->getId(),
                'roomId' => $rating->getRoomId()?->getId(),
            ];
        }, $ratings);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Rating $rating): JsonResponse
    {
        return $this->json([
            'id' => $rating->getId(),
            'rating' => $rating->getRating(),
            'comment' => $rating->getComment(),
            'date' => $rating->getDate()?->format('Y-m-d'),
            'authorId' => $rating->getAuthorId()?->getId(),
            'roomId' => $rating->getRoomId()?->getId(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        RoomRepository $roomRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $rating = new Rating();
        $rating->setRating($data['rating'] ?? 0)
               ->setComment($data['comment'] ?? '')
               ->setDate(new \DateTime($data['date'] ?? 'now'));

        if (isset($data['authorId'])) {
            $author = $userRepository->find($data['authorId']);
            $rating->setAuthorId($author);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepository->find($data['roomId']);
            $rating->setRoomId($room);
        }

        $em->persist($rating);
        $em->flush();

        return $this->json([
            'message' => 'Avis créé avec succès',
            'id' => $rating->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Rating $rating,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        RoomRepository $roomRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['rating'])) {
            $rating->setRating($data['rating']);
        }

        if (isset($data['comment'])) {
            $rating->setComment($data['comment']);
        }

        if (isset($data['date'])) {
            $rating->setDate(new \DateTime($data['date']));
        }

        if (isset($data['authorId'])) {
            $author = $userRepository->find($data['authorId']);
            $rating->setAuthorId($author);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepository->find($data['roomId']);
            $rating->setRoomId($room);
        }

        $em->flush();

        return $this->json(['message' => 'Avis mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Rating $rating, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($rating);
        $em->flush();

        return $this->json(['message' => 'Avis supprimé avec succès']);
    }
}
