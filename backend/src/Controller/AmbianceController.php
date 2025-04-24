<?php

namespace App\Controller;

use App\Entity\Ambiance;
use App\Repository\AmbianceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/ambiance', name: 'app_ambiance_')]
class AmbianceController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(AmbianceRepository $repository): JsonResponse
    {
        $ambiances = $repository->findAll();

        $data = array_map(fn(Ambiance $ambiance) => [
            'id' => $ambiance->getId(),
            'name' => $ambiance->getName(),
            'description' => $ambiance->getDescription(),
        ], $ambiances);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Ambiance $ambiance): JsonResponse
    {
        return $this->json([
            'id' => $ambiance->getId(),
            'name' => $ambiance->getName(),
            'description' => $ambiance->getDescription(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $ambiance = new Ambiance();
        $ambiance->setName($data['name'] ?? '');
        $ambiance->setDescription($data['description'] ?? '');

        $em->persist($ambiance);
        $em->flush();

        return $this->json([
            'message' => 'Ambiance créée avec succès',
            'id' => $ambiance->getId()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Ambiance $ambiance, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $ambiance->setName($data['name']);
        }
        if (isset($data['description'])) {
            $ambiance->setDescription($data['description']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Ambiance mise à jour avec succès'
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Ambiance $ambiance, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($ambiance);
        $em->flush();

        return $this->json([
            'message' => 'Ambiance supprimée avec succès'
        ]);
    }
}
