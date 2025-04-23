<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/hotel', name: 'app_hotel_')]
final class HotelController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(HotelRepository $repository): JsonResponse
    {
        $hotels = $repository->findAll();

        $data = array_map(function (Hotel $hotel) {
            return [
                'id' => $hotel->getId(),
                'name' => $hotel->getName(),
                'address' => $hotel->getAddress(),
                'city' => $hotel->getCity(),
                'country' => $hotel->getCountry(),
                'description' => $hotel->getDescription(),
            ];
        }, $hotels);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Hotel $hotel): JsonResponse
    {
        return $this->json([
            'id' => $hotel->getId(),
            'name' => $hotel->getName(),
            'address' => $hotel->getAddress(),
            'city' => $hotel->getCity(),
            'country' => $hotel->getCountry(),
            'description' => $hotel->getDescription(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $hotel = new Hotel();
        $hotel->setName($data['name'] ?? '')
              ->setAddress($data['address'] ?? '')
              ->setCity($data['city'] ?? '')
              ->setCountry($data['country'] ?? '')
              ->setDescription($data['description'] ?? '');

        $em->persist($hotel);
        $em->flush();

        return $this->json([
            'message' => 'Hôtel créé avec succès',
            'id' => $hotel->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Hotel $hotel, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $hotel->setName($data['name'] ?? $hotel->getName())
              ->setAddress($data['address'] ?? $hotel->getAddress())
              ->setCity($data['city'] ?? $hotel->getCity())
              ->setCountry($data['country'] ?? $hotel->getCountry())
              ->setDescription($data['description'] ?? $hotel->getDescription());

        $em->flush();

        return $this->json(['message' => 'Hôtel mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Hotel $hotel, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($hotel);
        $em->flush();

        return $this->json(['message' => 'Hôtel supprimé avec succès']);
    }
}
