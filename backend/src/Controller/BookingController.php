<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/booking', name: 'app_booking_')]
final class BookingController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(BookingRepository $repository): JsonResponse
    {
        $bookings = $repository->findAll();

        $data = array_map(function (Booking $booking) {
            return [
                'id' => $booking->getId(),
                'startDate' => $booking->getStartDate()->format('Y-m-d'),
                'endDate' => $booking->getEndDate()->format('Y-m-d'),
                'totalPrice' => $booking->getTotalPrice(),
                'status' => $booking->getStatus(),
                'userId' => $booking->getUserId()?->getId(),
                'roomId' => $booking->getRoomId()?->getId(),
            ];
        }, $bookings);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Booking $booking): JsonResponse
    {
        return $this->json([
            'id' => $booking->getId(),
            'startDate' => $booking->getStartDate()->format('Y-m-d'),
            'endDate' => $booking->getEndDate()->format('Y-m-d'),
            'totalPrice' => $booking->getTotalPrice(),
            'status' => $booking->getStatus(),
            'userId' => $booking->getUserId()?->getId(),
            'roomId' => $booking->getRoomId()?->getId(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepo,
        RoomRepository $roomRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $booking = new Booking();
        $booking->setStartDate(new \DateTime($data['startDate']));
        $booking->setEndDate(new \DateTime($data['endDate']));
        $booking->setTotalPrice($data['totalPrice']);
        $booking->setStatus($data['status']);

        if (isset($data['userId'])) {
            $user = $userRepo->find($data['userId']);
            $booking->setUserId($user);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepo->find($data['roomId']);
            $booking->setRoomId($room);
        }

        $em->persist($booking);
        $em->flush();

        return $this->json([
            'message' => 'Réservation créée avec succès',
            'id' => $booking->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Booking $booking,
        EntityManagerInterface $em,
        UserRepository $userRepo,
        RoomRepository $roomRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['startDate'])) {
            $booking->setStartDate(new \DateTime($data['startDate']));
        }

        if (isset($data['endDate'])) {
            $booking->setEndDate(new \DateTime($data['endDate']));
        }

        if (isset($data['totalPrice'])) {
            $booking->setTotalPrice($data['totalPrice']);
        }

        if (isset($data['status'])) {
            $booking->setStatus($data['status']);
        }

        if (isset($data['userId'])) {
            $user = $userRepo->find($data['userId']);
            $booking->setUserId($user);
        }

        if (isset($data['roomId'])) {
            $room = $roomRepo->find($data['roomId']);
            $booking->setRoomId($room);
        }

        $em->flush();

        return $this->json(['message' => 'Réservation mise à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Booking $booking, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($booking);
        $em->flush();

        return $this->json(['message' => 'Réservation supprimée avec succès']);
    }
}
