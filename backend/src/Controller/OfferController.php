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
    #[Route('/all', name: 'index', methods: ['GET'])]
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

    #[Route('', name: 'show', methods: ['GET'])]
    public function show(Request $request, OfferRepository $offerRepository, UserRepository $userRepository, RoomRepository $roomRepository): JsonResponse
    {
        // Récupérer les paramètres de la requête
        $userId = $request->query->get('userId');
        $roomId = $request->query->get('roomId');
        // Recherche de l'utilisateur
        $user = $userRepository->find($userId);
        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }
    
        // Recherche de la chambre
        $room = $roomRepository->find($roomId);
        if (!$room) {
            return $this->json([
                'message' => 'Chambre non trouvée'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Recherche de l'offre en fonction de userId et roomId
        $offer = $offerRepository->findOneBy([
            'userId' => $userId,
            'roomId' => $roomId,
        ]);
    
        // Vérifier si l'offre existe
        if (!$offer) {
            return $this->json([
                'message' => 'Offre non trouvée pour cet utilisateur et cette chambre'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Format de la réponse
        $data = [
            'id' => $offer->getId(),
            'proposedPrice' => $offer->getProposedPrice(),
            'status' => $offer->getStatus(),
            'offerDate' => $offer->getOfferDate()?->format('Y-m-d'),
            'user' => $user ? [
                'id' => $user->getId(),
                'name' => $user->getFirstName()
            ] : null,
            'room' => $room ? [
                'id' => $room->getId(),
                'name' => $room->getName()
            ] : null,
            'parentOffer' => $offer->getParentOffer() ? [
                'id' => $offer->getParentOffer()->getId(),
                'proposedPrice' => $offer->getParentOffer()->getProposedPrice(),
                'status' => $offer->getParentOffer()->getStatus(),
                'offerDate' => $offer->getParentOffer()->getOfferDate()?->format('Y-m-d'),
            ] : null,
        ];
        
        return $this->json($data);
    }

    #[Route('/{offerId}/children', name: 'show_childrens', methods: ['GET'])]
    public function showChildrens($offerId, OfferRepository $offerRepository): JsonResponse
    {
        // Recherche de l'offre principale par son ID
        $offer = $offerRepository->find($offerId);
    
        // Vérifie si l'offre existe
        if (!$offer) {
            return $this->json([
                'message' => 'Offre principale non trouvée',
            ], Response::HTTP_NOT_FOUND);
        }
    
        // Recherche des offres enfants liées à l'offre principale
        $childrenOffers = $offerRepository->findBy([
            'parentOffer' => $offerId,
        ]);
    
        // Si aucune offre enfant n'est trouvée
        if (empty($childrenOffers)) {
            return $this->json([
                'message' => 'Aucune offre enfant trouvée',
            ], Response::HTTP_NOT_FOUND);
        }
    
        // Renvoyer les données des offres enfants
        $childrenData = [];
        foreach ($childrenOffers as $childOffer) {
            $childrenData[] = [
                'id' => $childOffer->getId(),
                'proposedPrice' => $childOffer->getProposedPrice(),
                'status' => $childOffer->getStatus(),
                'offerDate' => $childOffer->getOfferDate()?->format('Y-m-d'),
                'userId' => $childOffer->getUserId()?->getId(),
                'roomId' => $childOffer->getRoomId()?->getId(),
            ];
        }
    
        return $this->json($childrenData);
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
