<?php

namespace App\Controller;

use App\Entity\Search;
use App\Repository\SearchRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/search', name: 'app_search_')]
final class SearchController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SearchRepository $repository): JsonResponse
    {
        $searches = $repository->findAll();

        $data = array_map(function (Search $search) {
            return [
                'id' => $search->getId(),
                'criteria' => $search->getCriteria(),
                'searchDate' => $search->getSearchDate()?->format('Y-m-d H:i:s'),
                'userId' => $search->getUserId()?->getId(),
            ];
        }, $searches);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Search $search): JsonResponse
    {
        return $this->json([
            'id' => $search->getId(),
            'criteria' => $search->getCriteria(),
            'searchDate' => $search->getSearchDate()?->format('Y-m-d H:i:s'),
            'userId' => $search->getUserId()?->getId(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $search = new Search();
        $search->setCriteria($data['criteria'] ?? '')
               ->setSearchDate(new \DateTime($data['searchDate'] ?? 'now'));

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $search->setUserId($user);
        }

        $em->persist($search);
        $em->flush();

        return $this->json([
            'message' => 'Recherche enregistrée avec succès',
            'id' => $search->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Search $search,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['criteria'])) {
            $search->setCriteria($data['criteria']);
        }

        if (isset($data['searchDate'])) {
            $search->setSearchDate(new \DateTime($data['searchDate']));
        }

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            $search->setUserId($user);
        }

        $em->flush();

        return $this->json(['message' => 'Recherche mise à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Search $search, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($search);
        $em->flush();

        return $this->json(['message' => 'Recherche supprimée avec succès']);
    }
}
