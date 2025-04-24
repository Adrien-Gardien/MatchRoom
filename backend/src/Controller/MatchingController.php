<?php

namespace App\Controller;

use App\Entity\Matching;
use App\Repository\MatchingRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/matching', name: 'app_matching_')]
final class MatchingController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(MatchingRepository $repository): JsonResponse
    {
        $matchings = $repository->findAll();

        $data = array_map(function (Matching $matching) {
            return [
                'id' => $matching->getId(),
                'type' => $matching->isType(),
                'matchDate' => $matching->getMatchDate()?->format('Y-m-d H:i:s'),
                'userId' => $matching->getUserId()?->getId(),
                'roomId' => $matching->getRoomId()?->getId(),
            ];
        }, $matchings);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Matching $matching): JsonResponse
    {
        return $this->json([
            'id' => $matching->getId(),
            'type' => $matching->isType(),
            'matchDate' => $matching->getMatchDate()?->format('Y-m-d H:i:s'),
            'userId' => $matching->getUserId()?->getId(),
            'roomId' => $matching->getRoomId()?->getId(),
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

        $matching = new Matching();
        $matching->setType($data['type'] ?? false)
                 ->setMatchDate(new \DateTime($data['matchDate'] ?? 'now'));

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $matching->setUserId($user);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepository->find($data['roomId']);
            $matching->setRoomId($room);
        }

        $em->persist($matching);
        $em->flush();

        return $this->json([
            'message' => 'Matching créé avec succès',
            'id' => $matching->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Matching $matching,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        RoomRepository $roomRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['type'])) {
            $matching->setType((bool) $data['type']);
        }

        if (isset($data['matchDate'])) {
            $matching->setMatchDate(new \DateTime($data['matchDate']));
        }

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $matching->setUserId($user);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepository->find($data['roomId']);
            $matching->setRoomId($room);
        }

        $em->flush();

        return $this->json(['message' => 'Matching mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Matching $matching, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($matching);
        $em->flush();

        return $this->json(['message' => 'Matching supprimé avec succès']);
    }
}
