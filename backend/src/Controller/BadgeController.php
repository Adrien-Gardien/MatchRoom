<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/badge', name: 'app_badge_')]
final class BadgeController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(BadgeRepository $repository): JsonResponse
    {
        $badges = $repository->findAll();

        $data = array_map(fn(Badge $badge) => [
            'id' => $badge->getId(),
            'namename' => $badge->getNamename(),
            'description' => $badge->getDescription(),
            'image' => $badge->getImage(),
        ], $badges);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Badge $badge): JsonResponse
    {
        return $this->json([
            'id' => $badge->getId(),
            'namename' => $badge->getNamename(),
            'description' => $badge->getDescription(),
            'image' => $badge->getImage(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $badge = new Badge();
        $badge->setNamename($data['namename'] ?? '');
        $badge->setDescription($data['description'] ?? '');
        $badge->setImage($data['image'] ?? '');

        $em->persist($badge);
        $em->flush();

        return $this->json([
            'message' => 'Badge créé avec succès',
            'id' => $badge->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Badge $badge, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['namename'])) {
            $badge->setNamename($data['namename']);
        }
        if (isset($data['description'])) {
            $badge->setDescription($data['description']);
        }
        if (isset($data['image'])) {
            $badge->setImage($data['image']);
        }

        $em->flush();

        return $this->json(['message' => 'Badge mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Badge $badge, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($badge);
        $em->flush();

        return $this->json(['message' => 'Badge supprimé avec succès']);
    }
}
