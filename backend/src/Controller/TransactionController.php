<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/transaction', name: 'app_transaction_')]
final class TransactionController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(TransactionRepository $repository): JsonResponse
    {
        $transactions = $repository->findAll();

        $data = array_map(function (Transaction $transaction) {
            return [
                'id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'method' => $transaction->getMethod(),
                'status' => $transaction->getStatus(),
                'date' => $transaction->getDate()->format('Y-m-d H:i:s'),
                'bookingId' => $transaction->getBookingId() ? $transaction->getBookingId()->getId() : null,
            ];
        }, $transactions);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Transaction $transaction): JsonResponse
    {
        return $this->json([
            'id' => $transaction->getId(),
            'amount' => $transaction->getAmount(),
            'method' => $transaction->getMethod(),
            'status' => $transaction->getStatus(),
            'date' => $transaction->getDate()->format('Y-m-d H:i:s'),
            'bookingId' => $transaction->getBookingId() ? $transaction->getBookingId()->getId() : null,
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        BookingRepository $bookingRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $transaction = new Transaction();
        $transaction->setAmount($data['amount'] ?? '')
                    ->setMethod($data['method'] ?? '')
                    ->setStatus($data['status'] ?? '')
                    ->setDate(new \DateTime($data['date'] ?? 'now'));

        if (isset($data['bookingId'])) {
            $booking = $bookingRepository->find($data['bookingId']);
            if ($booking) {
                $transaction->setBookingId($booking);
            }
        }

        $em->persist($transaction);
        $em->flush();

        return $this->json([
            'message' => 'Transaction créée avec succès',
            'id' => $transaction->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $em,
        BookingRepository $bookingRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['amount'])) {
            $transaction->setAmount($data['amount']);
        }

        if (isset($data['method'])) {
            $transaction->setMethod($data['method']);
        }

        if (isset($data['status'])) {
            $transaction->setStatus($data['status']);
        }

        if (isset($data['date'])) {
            $transaction->setDate(new \DateTime($data['date']));
        }

        if (isset($data['bookingId'])) {
            $booking = $bookingRepository->find($data['bookingId']);
            if ($booking) {
                $transaction->setBookingId($booking);
            }
        }

        $em->flush();

        return $this->json(['message' => 'Transaction mise à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Transaction $transaction, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($transaction);
        $em->flush();

        return $this->json(['message' => 'Transaction supprimée avec succès']);
    }
}
