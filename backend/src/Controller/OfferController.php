<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/offer', name: 'app_offer_')]
final class OfferController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(OfferRepository $repository): JsonResponse
    {
        $offers = $repository->findAll();

        $data = array_map(function (Offer $offer) {
            return [
                'id' => $offer->getId(),
                'proposedPrice' => $offer->getProposedPrice(),
                'status' => $offer->getStatus(),
                'offerDate' => $offer->getOfferDate()?->format('Y-m-d'),
                'userId' => $offer->getUserId()?->getId(),
                'roomId' => $offer->getRoomId()?->getId(),
            ];
        }, $offers);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Offer $offer): JsonResponse
    {
        return $this->json([
            'id' => $offer->getId(),
            'proposedPrice' => $offer->getProposedPrice(),
            'status' => $offer->getStatus(),
            'offerDate' => $offer->getOfferDate()?->format('Y-m-d'),
            'userId' => $offer->getUserId()?->getId(),
            'roomId' => $offer->getRoomId()?->getId(),
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

        $offer = new Offer();
        $offer->setProposedPrice($data['proposedPrice'] ?? '0.00')
              ->setStatus($data['status'] ?? 'pending')
              ->setOfferDate(new \DateTime($data['offerDate'] ?? 'now'));

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $offer->setUserId($user);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepository->find($data['roomId']);
            $offer->setRoomId($room);
        }

        $em->persist($offer);
        $em->flush();

        return $this->json([
            'message' => 'Offre créée avec succès',
            'id' => $offer->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Offer $offer,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        RoomRepository $roomRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['proposedPrice'])) {
            $offer->setProposedPrice($data['proposedPrice']);
        }

        if (isset($data['status'])) {
            $offer->setStatus($data['status']);
        }

        if (isset($data['offerDate'])) {
            $offer->setOfferDate(new \DateTime($data['offerDate']));
        }

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $offer->setUserId($user);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepository->find($data['roomId']);
            $offer->setRoomId($room);
        }

        $em->flush();

        return $this->json(['message' => 'Offre mise à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Offer $offer, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($offer);
        $em->flush();

        return $this->json(['message' => 'Offre supprimée avec succès']);
    }
}
