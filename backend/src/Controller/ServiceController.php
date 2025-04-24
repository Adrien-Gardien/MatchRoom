<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use App\Repository\RoomRepository;
use App\Repository\UserPreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/service', name: 'app_service_')]
final class ServiceController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ServiceRepository $repository): JsonResponse
    {
        $services = $repository->findAll();

        $data = array_map(function (Service $service) {
            return [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'description' => $service->getDescription(),
            ];
        }, $services);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Service $service): JsonResponse
    {
        return $this->json([
            'id' => $service->getId(),
            'name' => $service->getName(),
            'description' => $service->getDescription(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        RoomRepository $roomRepository,
        UserPreferenceRepository $userPreferenceRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $service = new Service();
        $service->setName($data['name'] ?? '')
                ->setDescription($data['description'] ?? '');

        if (isset($data['rooms'])) {
            foreach ($data['rooms'] as $roomId) {
                $room = $roomRepository->find($roomId);
                if ($room) {
                    $service->addRoom($room);
                }
            }
        }

        if (isset($data['userPreferences'])) {
            foreach ($data['userPreferences'] as $userPrefId) {
                $userPreference = $userPreferenceRepository->find($userPrefId);
                if ($userPreference) {
                    $service->addUserPreference($userPreference);
                }
            }
        }

        $em->persist($service);
        $em->flush();

        return $this->json([
            'message' => 'Service créé avec succès',
            'id' => $service->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Service $service,
        EntityManagerInterface $em,
        RoomRepository $roomRepository,
        UserPreferenceRepository $userPreferenceRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $service->setName($data['name']);
        }

        if (isset($data['description'])) {
            $service->setDescription($data['description']);
        }

        if (isset($data['rooms'])) {
            foreach ($data['rooms'] as $roomId) {
                $room = $roomRepository->find($roomId);
                if ($room && !$service->getRooms()->contains($room)) {
                    $service->addRoom($room);
                }
            }
        }

        if (isset($data['userPreferences'])) {
            foreach ($data['userPreferences'] as $userPrefId) {
                $userPreference = $userPreferenceRepository->find($userPrefId);
                if ($userPreference && !$service->getUserPreferences()->contains($userPreference)) {
                    $service->addUserPreference($userPreference);
                }
            }
        }

        $em->flush();

        return $this->json(['message' => 'Service mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Service $service, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($service);
        $em->flush();

        return $this->json(['message' => 'Service supprimé avec succès']);
    }
}
